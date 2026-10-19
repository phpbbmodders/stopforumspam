# Stop Forum Spam — Historical Feature Requests and Improvement Review

**Repository:** [https://github.com/phpbbmodders/stopforumspam](https://github.com/phpbbmodders/stopforumspam)\
**Original Maintainer:** Rich McGirr (rmcgirr83)\
**Current Organization:** phpBBModders\
**Target Compatibility:** phpBB 3.3.19+ and phpBB 4.0 where supported\
**Proposed Documentation:** `docs/TODO.md`\
**Status:** Proposal — pending verification against the current implementation

## Overview

Transferred from `worthlookingat.md`. Historical issue summaries, feasibility ratings, API behavior, and compatibility claims have not been reverified during this move. These are review candidates, not approval to implement them.

A review of the repository's closed issues and pull requests identified several feature requests and improvements worth reconsidering.

Some were explicitly declined by the original maintainer, while others were deferred, closed without implementation, superseded, or rejected because the proposed implementation was unsuitable.

The goal is not to reopen every historical request. Instead, feasible improvements should be recorded in `docs/TODO.md`, with direct links to the original discussions and a clear indication of what still needs investigation.

## 1. Check Posts from Newly Registered Users

**Original Issue:** [https://github.com/phpbbmodders/stopforumspam/issues/9](https://github.com/phpbbmodders/stopforumspam/issues/9)\
**Priority:** High\
**Feasibility:** High\
**Status:** Needs verification

### Background

Spammers may successfully register before their information appears in the Stop Forum Spam database. Their information may subsequently be reported before they make their first post.

The original maintainer questioned whether additional checking was necessary when moderators already review posts from newly registered users.

### Proposed Improvement

Allow administrators to enable additional SFS checks for recently registered accounts.

Possible conditions include:

- Membership in phpBB's Newly Registered Users group.
- Account age.
- Number of approved posts.
- Configurable maximum number of checks.

Checks should respect existing SFS thresholds and failure-handling settings.

Established members should not be checked unnecessarily.

**TODO Recommendation:** Add as a candidate feature after verifying that equivalent functionality does not already exist.

## 2. Report Confirmed Spammers from ACP User Management

**Original Issue:** [https://github.com/phpbbmodders/stopforumspam/issues/53](https://github.com/phpbbmodders/stopforumspam/issues/53)\
**Original PR:** [https://github.com/phpbbmodders/stopforumspam/pull/7](https://github.com/phpbbmodders/stopforumspam/pull/7)\
**Related Historical Discussion:** [https://github.com/phpbbmodders/stopforumspam/issues/3](https://github.com/phpbbmodders/stopforumspam/issues/3)\
**Priority:** High\
**Feasibility:** High\
**Status:** Needs design

### Background

The original proposal would allow administrators to report spammers directly from phpBB's ACP user management interface.

The maintainer declined the original approach because Stop Forum Spam reporting must be based on verified spam activity rather than a database match or suspicion alone.

The discussion subsequently considered including actual spam content as evidence.

### Proposed Improvement

Provide an ACP reporting action for confirmed spam accounts.

The workflow should:

1. Verify administrator permissions.
2. Identify the account being reported.
3. Require confirmation that spam activity has been verified.
4. Allow supporting evidence to be entered.
5. Optionally populate evidence from an existing spam post.
6. Validate the required reporting information.
7. Submit through the existing SFS API integration.
8. Display the result and log the action.

Prevent duplicate submissions where practical.

A user's presence in the SFS database must never automatically trigger a new report.

Current SFS reporting requirements must be verified before implementation.

**TODO Recommendation:** Add as a high-priority candidate requiring an evidence-based design.

## 3. Configurable Ban Duration

**Original PR:** [https://github.com/phpbbmodders/stopforumspam/pull/13](https://github.com/phpbbmodders/stopforumspam/pull/13)\
**Priority:** Medium\
**Feasibility:** High\
**Status:** Needs verification

### Background

A contributor proposed allowing administrators to configure ban duration in the ACP.

The PR was closed without merging, but the available discussion does not establish a clear reason for rejection.

### Proposed Improvement

Where the extension creates actual phpBB bans, allow administrators to select their duration.

Possible options:

- Permanent.
- One day.
- Seven days.
- Thirty days.
- Custom duration.

The setting should apply only to bans created by Stop Forum Spam.

Existing bans should not be modified retroactively.

First verify whether the current extension uses persistent phpBB bans for the relevant blocking action.

**TODO Recommendation:** Add as a conditional improvement.

## 4. Configurable Stop Forum Spam API Endpoint

Follow up under [Choice of Stop Forum Spam server](../TODO.md#choice-of-stop-forum-spam-server); this is historical context for that existing task.

**Original Issue:** [https://github.com/phpbbmodders/stopforumspam/issues/58](https://github.com/phpbbmodders/stopforumspam/issues/58)\
**Priority:** Medium\
**Feasibility:** High\
**Status:** Verify endpoint support

### Background

An administrator requested the ability to choose the European Stop Forum Spam API endpoint.

### Proposed Improvement

Provide an ACP setting allowing selection between officially supported SFS endpoints.

Requirements:

- Default to the standard endpoint.
- Offer the European endpoint if currently supported.
- Preserve authentication, timeouts, and existing API behavior.
- Validate endpoint selection.
- Avoid unrestricted custom API URLs by default.

Using a European endpoint should not be presented as guaranteeing regulatory compliance.

**TODO Recommendation:** Add for investigation and implementation if the endpoint remains officially supported.

## 5. Improved API Error Handling and Diagnostics

**Original Issue:** [https://github.com/phpbbmodders/stopforumspam/issues/45](https://github.com/phpbbmodders/stopforumspam/issues/45)\
**Original PR:** [https://github.com/phpbbmodders/stopforumspam/pull/46](https://github.com/phpbbmodders/stopforumspam/pull/46)\
**Related PR:** [https://github.com/phpbbmodders/stopforumspam/pull/62](https://github.com/phpbbmodders/stopforumspam/pull/62)\
**Priority:** High\
**Feasibility:** High\
**Status:** Audit current implementation

### Background

The original issue identified misleading error messages when API requests failed.

An unsuccessful request could be reported as an SFS outage even when the actual cause was local configuration, DNS, networking, or TLS verification.

The original implementation PR was declined, although the maintainer acknowledged the problem.

### Proposed Improvement

Differentiate between:

- Connection timeouts.
- DNS failures.
- TLS certificate errors.
- HTTP errors.
- Invalid API responses.
- Authentication failures.
- Service unavailability.

Consider adding an ACP connection-test function that reports diagnostic information without exposing credentials.

Recent changes have addressed some API and cURL error handling, so the current code should be reviewed before implementing anything new.

**TODO Recommendation:** Add as an audit and follow-up task.

## 6. Improved Spam-Check Logging

**Original Issue:** [https://github.com/phpbbmodders/stopforumspam/issues/25](https://github.com/phpbbmodders/stopforumspam/issues/25)\
**Related Discussion:** [https://github.com/phpbbmodders/stopforumspam/issues/20](https://github.com/phpbbmodders/stopforumspam/issues/20)\
**Priority:** Medium\
**Feasibility:** Medium\
**Status:** Needs design

### Background

Administrators have requested better visibility into why spam registrations or posts were allowed.

Existing logging may explain blocked requests without giving administrators sufficient insight into successful or skipped checks.

### Proposed Improvement

Consider recording meaningful outcomes:

- SFS match found.
- No match found.
- Lookup skipped.
- API unavailable.
- Request allowed because of configured thresholds.
- Request blocked.
- Report submitted.
- Report failed.

Use existing phpBB logging facilities where possible.

Avoid excessive logging, unnecessary personal information, and exposure of API credentials.

**TODO Recommendation:** Add as a candidate for configurable diagnostic logging.

## 7. Reporting Confirmation and Duplicate Prevention

**Original Issue:** [https://github.com/phpbbmodders/stopforumspam/issues/36](https://github.com/phpbbmodders/stopforumspam/issues/36)\
**Related Security PRs:**

- [https://github.com/phpbbmodders/stopforumspam/pull/60](https://github.com/phpbbmodders/stopforumspam/pull/60)
- [https://github.com/phpbbmodders/stopforumspam/pull/61](https://github.com/phpbbmodders/stopforumspam/pull/61)
- [https://github.com/phpbbmodders/stopforumspam/pull/62](https://github.com/phpbbmodders/stopforumspam/pull/62)

**Priority:** Medium\
**Feasibility:** High\
**Status:** Audit existing implementation

### Background

The original request proposed a confirmation dialog before reporting a post to Stop Forum Spam.

The purpose was to prevent accidental submissions.

### Proposed Improvement

Review reporting workflows for:

- Explicit confirmation.
- Appropriate authorization.
- Correct target identification.
- Duplicate-report prevention.
- Clear success and failure messages.
- Protection against repeated submissions.

Recent security PRs have addressed authorization and reporting behavior. Those changes must be considered before proposing additional work.

**TODO Recommendation:** Add as a reporting-workflow audit, not an assumed missing feature.

## 8. Improved Blocked-User Messages

**Original Issue:** [https://github.com/phpbbmodders/stopforumspam/issues/20](https://github.com/phpbbmodders/stopforumspam/issues/20)\
**Related PRs:**

- [https://github.com/phpbbmodders/stopforumspam/pull/69](https://github.com/phpbbmodders/stopforumspam/pull/69)
- [https://github.com/phpbbmodders/stopforumspam/pull/71](https://github.com/phpbbmodders/stopforumspam/pull/71)

**Priority:** Low–Medium\
**Feasibility:** High\
**Status:** Needs verification

### Background

The original discussion raised concerns about confusing messages shown to blocked users.

More recent work has addressed Contact Admin integration and alternative contact information.

### Proposed Improvement

Review current messages to ensure they:

- Explain why the action was blocked without exposing sensitive detection rules.
- Distinguish confirmed blocking from temporary API failures.
- Provide an appropriate administrator contact method.
- Avoid exposing contact addresses unnecessarily.
- Remain translatable.

**TODO Recommendation:** Add only if gaps remain after reviewing recent changes.

## 11. Additional Historical Requests Worth Checking

The following issues and PRs should be reviewed for completeness, but they should not automatically become new feature tasks.

| Issue / PR                                                                                                         | Subject                                         | Suggested Action                         |
| ------------------------------------------------------------------------------------------------------------------ | ----------------------------------------------- | ---------------------------------------- |
| [https://github.com/phpbbmodders/stopforumspam/issues/26](https://github.com/phpbbmodders/stopforumspam/issues/26) | Private-message spam handling                   | Verify existing implementation           |
| [https://github.com/phpbbmodders/stopforumspam/issues/27](https://github.com/phpbbmodders/stopforumspam/issues/27) | Git tags and release packaging                  | Check current release workflow           |
| [https://github.com/phpbbmodders/stopforumspam/issues/3](https://github.com/phpbbmodders/stopforumspam/issues/3)   | SFS reporting terms and evidence                | Preserve reporting safeguards            |
| [https://github.com/phpbbmodders/stopforumspam/pull/24](https://github.com/phpbbmodders/stopforumspam/pull/24)     | HTTPS links                                     | Verify superseded by newer HTTPS changes |
| [https://github.com/phpbbmodders/stopforumspam/pull/63](https://github.com/phpbbmodders/stopforumspam/pull/63)     | HTTPS links in language files                   | Verify current implementation            |
| [https://github.com/phpbbmodders/stopforumspam/issues/19](https://github.com/phpbbmodders/stopforumspam/issues/19) | Expensive administrator/moderator cache queries | Verify performance fixes                 |
| [https://github.com/phpbbmodders/stopforumspam/issues/56](https://github.com/phpbbmodders/stopforumspam/issues/56) | Site unresponsiveness caused by cache refresh   | Verify current performance fixes         |
| [https://github.com/phpbbmodders/stopforumspam/pull/57](https://github.com/phpbbmodders/stopforumspam/pull/57)     | Avoid board-wide ACL scans on every request     | Verify merged implementation and tests   |

These are primarily verification tasks. Features or fixes already implemented should not be duplicated.

## Proposed Addition to `docs/TODO.md`

**Suggested heading:** Historical Feature Requests and Improvement Candidates

Add the following entries, preserving the original issue and PR URLs.

| Candidate                         | References                                                                                                                                                                                                                            | Priority   | Status               |
| --------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------- | -------------------- |
| Newly registered user post checks | [https://github.com/phpbbmodders/stopforumspam/issues/9](https://github.com/phpbbmodders/stopforumspam/issues/9)                                                                                                                      | High       | Needs verification   |
| Evidence-based ACP reporting      | [https://github.com/phpbbmodders/stopforumspam/issues/53](https://github.com/phpbbmodders/stopforumspam/issues/53) and [https://github.com/phpbbmodders/stopforumspam/pull/7](https://github.com/phpbbmodders/stopforumspam/pull/7)   | High       | Needs design         |
| API error diagnostics             | [https://github.com/phpbbmodders/stopforumspam/issues/45](https://github.com/phpbbmodders/stopforumspam/issues/45) and [https://github.com/phpbbmodders/stopforumspam/pull/46](https://github.com/phpbbmodders/stopforumspam/pull/46) | High       | Audit implementation |
| Configurable ban duration         | [https://github.com/phpbbmodders/stopforumspam/pull/13](https://github.com/phpbbmodders/stopforumspam/pull/13)                                                                                                                        | Medium     | Needs verification   |
| European API endpoint             | [https://github.com/phpbbmodders/stopforumspam/issues/58](https://github.com/phpbbmodders/stopforumspam/issues/58)                                                                                                                    | Medium     | Verify support       |
| Spam-check logging                | [https://github.com/phpbbmodders/stopforumspam/issues/25](https://github.com/phpbbmodders/stopforumspam/issues/25)                                                                                                                    | Medium     | Needs design         |
| Reporting safeguards              | [https://github.com/phpbbmodders/stopforumspam/issues/36](https://github.com/phpbbmodders/stopforumspam/issues/36)                                                                                                                    | Medium     | Audit implementation |
| Blocked-user messages             | [https://github.com/phpbbmodders/stopforumspam/issues/20](https://github.com/phpbbmodders/stopforumspam/issues/20)                                                                                                                    | Low–Medium | Needs verification   |

### Suggested Status Definitions

**Needs Verification:** Determine whether the functionality already exists.

**Needs Design:** The idea is feasible but needs a defined implementation.

**Audit Implementation:** Review current code, tests, and related merged PRs.

**Approved:** Accepted for implementation.

**Implemented:** Completed and verified.

**Not Pursuing:** Reviewed and excluded with a documented reason.

## Implementation Guidelines

Before implementing a historical request:

1. Inspect the current source code.
2. Check the original issue and all related PR discussions.
3. Confirm whether equivalent functionality has already been implemented.
4. Verify compatibility with phpBB 3.3.19+ and phpBB 4.0 where applicable.
5. Verify current SFS API requirements.
6. Keep unrelated improvements in separate PRs.
7. Add appropriate automated tests.
8. Update documentation and language strings.
9. Preserve links to the historical discussions.

Original issues should generally remain closed. The TODO should track newly approved work without unnecessarily reopening historical discussions.

## Final Recommendation

Add the feasible historical requests to `docs/TODO.md` as a structured review backlog.

Prioritize newly registered user checks, evidence-based ACP reporting, API diagnostics, and improved logging.

Consider configurable ban duration, regional API endpoints, reporting safeguards, and blocked-user messaging after verifying current functionality.

Translation handling should be addressed through a consistent phpBBModders policy, while Contact Admin-specific protections should remain outside this extension.

**The immediate goal should be documenting and verifying these opportunities, not implementing all of them at once.**

Every TODO entry should retain a direct link to its originating GitHub issue or pull request so future contributors can review the original discussion, understand previous decisions, and avoid repeating completed work.

# Stop Forum Spam — Matching Controls

Review the existing Stop Forum Spam extension's matching configuration before adding new options.

Potential improvements, only where not already supported:

- Minimum report-frequency threshold.
- Maximum age of a report or last-seen threshold.
- Different handling for IP, email, and username matches.
- Clearer explanations of which criteria caused a registration to be rejected.

These controls belong in **phpbbmodders/stopforumspam**, not SFS Companion.

They should be treated as a separate enhancement from the Registration Activity page.
