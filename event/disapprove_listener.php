<?php
/**
 *
 * Stop Forum Spam extension for the phpBB Forum Software package
 *
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\stopforumspam\event;

use phpbb\config\config;
use phpbb\language\language;
use phpbb\log\log;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use phpbbmodders\stopforumspam\core\sfsgroups as sfsgroups;
use phpbbmodders\stopforumspam\core\sfsapi as sfsapi;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Report the posters of disapproved posts to Stop Forum Spam.
 *
 * Adds a "Report to Stop Forum Spam" checkbox to the screen shown when
 * disapproving posts from the moderation queue (template events
 * mcp_approve_reason_after and mcp_approve_ajax_reason_after, added in
 * phpBB 3.3.20). On boards without those events the checkbox simply doesn't
 * appear. When it is ticked, each disapproved post's poster is reported once,
 * the same way the "Report to Stop Forum Spam" button reports a post.
 */
class disapprove_listener implements EventSubscriberInterface
{
	/** Name of the checkbox on the disapprove form */
	const CHECKBOX = 'sfs_report_disapproved';

	/** @var config */
	protected $config;

	/** @var language */
	protected $language;

	/** @var log */
	protected $log;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var sfsgroups */
	protected $sfsgroups;

	/** @var sfsapi */
	protected $sfsapi;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	public function __construct(
		config $config,
		language $language,
		log $log,
		request $request,
		template $template,
		user $user,
		sfsgroups $sfsgroups,
		sfsapi $sfsapi,
		$root_path,
		$php_ext)
	{
		$this->config = $config;
		$this->language = $language;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->sfsgroups = $sfsgroups;
		$this->sfsapi = $sfsapi;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup_after'			=> 'show_checkbox',
			'core.disapprove_posts_after'	=> 'report_disapproved',
		];
	}

	/**
	 * Is reporting possible at all: extension enabled and an API key set
	 *
	 * @return bool
	 */
	protected function reporting_enabled()
	{
		return !empty($this->config['allow_sfs']) && !empty($this->config['sfs_api_key']);
	}

	/**
	 * Let the disapprove form show the checkbox on moderator pages
	 *
	 * Only mcp.php can show the disapprove form, and reaching it already
	 * requires the m_approve permission there.
	 *
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function show_checkbox($event)
	{
		if ($this->user->page['page_name'] == 'mcp.' . $this->php_ext && $this->reporting_enabled())
		{
			$this->template->assign_var('S_SFS_REPORT_DISAPPROVED', true);
		}
	}

	/**
	 * Report the posters of the posts just disapproved, if the box was ticked
	 *
	 * Runs after the posts are deleted; post_info holds their data from
	 * before the deletion.
	 *
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function report_disapproved($event)
	{
		if (!$event['is_disapproving'] || !$this->reporting_enabled() || !$this->request->variable(self::CHECKBOX, false))
		{
			return;
		}

		if (!function_exists('generate_text_for_display'))
		{
			include($this->root_path . 'includes/functions_content.' . $this->php_ext);
		}

		$reported = [];
		foreach ($event['post_info'] as $post)
		{
			$poster_id = (int) $post['poster_id'];

			// One report per poster, and never for guests, staff, posts already
			// reported, or posts whose IP was anonymized or is missing
			if (isset($reported[$poster_id]) || $poster_id == ANONYMOUS || !empty($post['sfs_reported'])
				|| empty($post['user_email'])
				|| filter_var($post['poster_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) === false
				|| in_array($poster_id, $this->sfsgroups->getadminsmods((int) $post['forum_id'])))
			{
				continue;
			}
			$reported[$poster_id] = true;

			$parse_flags = ($post['bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | ($post['enable_smilies'] ? OPTION_FLAG_SMILIES : 0);
			$evidence = generate_text_for_display($post['post_text'], $post['bbcode_uid'], $post['bbcode_bitfield'], $parse_flags, true);

			$response = $this->sfsapi->sfsapi('add', $post['username'], $post['poster_ip'], $post['user_email'], $evidence, $this->config['sfs_api_key']);
			$json_decode = is_string($response) ? json_decode($response, true) : null;

			// The post is already disapproved; record the failed report rather than hide it
			if (!$response || isset($json_decode[sfsapi::CURL_ERROR_KEY]))
			{
				$this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_SFS_REPORT_FAILED', false, [
					$post['username'],
					'forum_id'	=> (int) $post['forum_id'],
					'topic_id'	=> (int) $post['topic_id'],
				]);
				continue;
			}

			$this->sfsapi->sfs_ban('user', $post['username']);

			$this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_SFS_REPORTED', false, [
				$this->language->lang('SFS_USERNAME_STOPPED', $post['username']),
				'forum_id'	=> (int) $post['forum_id'],
				'topic_id'	=> (int) $post['topic_id'],
			]);
		}
	}
}
