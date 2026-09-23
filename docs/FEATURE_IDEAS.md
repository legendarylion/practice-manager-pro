# Practice Manager Pro — Feature Ideas

A running list of feature ideas to evaluate for the roadmap. Each entry includes
the problem it solves, the decision it supports, dependencies, and a rough
complexity estimate (S / M / L) to help with planning.

This is a working document — add freely, prune when something gets built or
abandoned. Order does not imply priority.

---

## Already in the app

| Feature | State |
|---|---|
| Practice settings | CRUD shell exists |
| Event types (bookable services) | CRUD shell, no booking flow yet |
| Appointments | CRUD shell |
| Availability (weekly hours) | Index + store only |
| Clinicians | Full CRUD with dark-mode UI |
| Performance logs (quarterly/annual KPIs) | Full CRUD with live preview |
| Dashboard (BI prototype) | Rich mock UI — AI briefing, pulse, goal pacing, cashflow, funnel, clinician P&L, payer mix, retention, no-shows, recommendations, hire model, anomalies |
| Jetstream auth baseline | Login, register, profile, 2FA |

---

## Theme 1 — Cash flow & revenue intelligence

Owners' #1 stressor is cash flow predictability, not revenue. Insurance pays
30–90 days late and payer profitability varies wildly. Right now we only
capture quarterly aggregate revenue; the dashboard prototype implies a deeper
data layer.

### 1.1 Payer / Insurance tracking (M)
- **Problem:** A session billed at $200 might net $89 from BCBS, $140 from Cigna, $200 from private pay. Owners often don't know which payers are profitable.
- **Captures:** Payers table, per-appointment billed vs collected, contracted rates, claim status (submitted / paid / denied / written off).
- **Decision:** Which payers to keep, drop, or renegotiate.
- **Dependencies:** Appointments must reference a payer; sessions need a "billed amount" and "collected amount" pair.

### 1.2 AR aging (M)
- **Problem:** Owners can't see what cash is in-flight vs. stuck.
- **Captures:** Per-payer outstanding balance, aging buckets (0–30, 31–60, 61–90, 90+).
- **Decision:** Which claims to follow up on, when to write off, whether to fire a payer.
- **Dependencies:** Payer tracking (1.1).

### 1.3 Owner take-home calculator (S)
- **Problem:** Revenue ≠ what hits the owner's bank account. Most owners conflate the two.
- **Captures:** Payroll, taxes, overhead, draws — computed from existing PerformanceLog fields plus a small `owner_compensation` table.
- **Decision:** Whether the practice is paying *the owner* enough.
- **Dependencies:** None — sits on top of PerformanceLog.

### 1.4 Cash flow forecast (M)
- **Problem:** "Will I make payroll next month?" requires a forward-looking view.
- **Captures:** Projected inflows by week from current AR + expected new sessions × historical collection rate.
- **Decision:** Hiring, marketing spend, expansion timing.
- **Dependencies:** 1.1, 1.2.

---

## Theme 2 — Client lifecycle

The intake funnel is mostly invisible today and contains the highest-leverage
optimization opportunities.

### 2.1 Lead / inquiry capture (M)
- **Problem:** Owners don't track inquiry → booked client conversion.
- **Captures:** Lead records with source (Psychology Today, Google, referral, etc.), stage (inquiry → consult → first session → retained), drop-off reasons.
- **Decision:** Where the funnel leaks, where to invest marketing.
- **Dependencies:** None — new model.

### 2.2 Referral source attribution (S, after 2.1)
- **Problem:** Which marketing channels produce the highest-LTV clients? Almost nobody knows.
- **Captures:** Referral source on each lead → carries through to appointments → enables LTV-by-source reporting.
- **Decision:** Marketing budget allocation.
- **Dependencies:** 2.1.

### 2.3 Waitlist management (S)
- **Problem:** Most practices have a waitlist but no system. People drop off because no one followed up.
- **Captures:** Waitlist entries with preferences (clinician, insurance, virtual/in-person), priority, last contact.
- **Decision:** Who to call when a slot opens.
- **Dependencies:** None.

### 2.4 At-risk client detection (M)
- **Problem:** Retention is the #1 hidden lever. Clients who miss 2 sessions often disappear.
- **Captures:** Computed signal — missed sessions, payment failures, no future booking, gap > 14 days.
- **Decision:** Who to re-engage this week.
- **Dependencies:** Real appointment data with statuses (already in schema).

### 2.5 Cohort retention analysis (M)
- **Problem:** Average sessions per client is a single number; cohort retention curves are actionable.
- **Captures:** Computed view of week-4 / week-8 / week-12 retention by intake month, by clinician, by referral source.
- **Decision:** Which clinicians and intake processes drive durable relationships.
- **Dependencies:** Real appointment data.

---

## Theme 3 — Operational drag

### 3.1 No-show / late cancellation tracking (S)
- **Problem:** Industry no-show rate is 10–15%. At $150/session that's $1.5–3K/month per clinician.
- **Captures:** Already in the appointments status enum (`cancelled`). Need a distinct `no_show` value plus optional `cancelled_at` timestamp.
- **Decision:** Which clinicians, days, times correlate with no-shows; whether to enforce late-cancel fees.
- **Dependencies:** None — small schema add.

### 3.2 Compliance / credentialing tracker (M)
- **Problem:** Licenses, CE hours, supervision hours, payer credentialing, NPI updates, malpractice. Missing one costs revenue.
- **Captures:** Credentials per clinician with expiration dates, document uploads, reminder schedule.
- **Decision:** What to renew when; who's about to fall out of network.
- **Dependencies:** None.
- **Stickiness note:** Once this is in the system, practices don't leave.

### 3.3 Owner time / role tracking (S)
- **Problem:** Owners often spend 60% on clinical work when they should be at 30%. They can't see it.
- **Captures:** Time logged against categories (clinical, admin, marketing, strategy).
- **Decision:** Whether to delegate, hire admin, raise rates.
- **Dependencies:** None — lightweight model.

---

## Theme 4 — Hiring & clinician economics

### 4.1 Per-clinician P&L (M)
- **Problem:** "Is this hire working?" needs more than utilization.
- **Captures:** Compensation model (W2 salary, 1099 split %, fixed-per-session), overhead allocation, payer-mix-adjusted revenue.
- **Decision:** Keep, coach, or let go.
- **Dependencies:** Payer tracking (1.1) for accurate revenue attribution.

### 4.2 "Should I hire?" calculator (M)
- **Problem:** Owners agonize over this decision with no model.
- **Captures:** Cost of new hire (salary/split + credentialing time + onboarding + room) → breakeven months → year-one contribution.
- **Decision:** Hire / don't hire / hire part-time.
- **Dependencies:** 4.1 for the cost-model template; capacity data (already exists).

### 4.3 Burnout indicators (S)
- **Problem:** Sustained >95% utilization for 8+ weeks is a leading indicator of clinician churn.
- **Captures:** Computed flag from existing utilization + no-show rate + session-rating data (if collected).
- **Decision:** Who to give a break to before they leave.
- **Dependencies:** No-show tracking (3.1) ideally.

---

## Theme 5 — Owner strategy

### 5.1 Goal tracking + variance alerts (S)
- **Problem:** Owners set annual goals but operate quarter-to-quarter; variance creeps in unnoticed.
- **Captures:** Goals table (target metric, target value, deadline) + computed variance from current pace.
- **Decision:** When and how to course-correct.
- **Dependencies:** None — uses existing PerformanceLog data.

### 5.2 Revenue forecasting (S)
- **Problem:** No forward view — only retrospective KPIs.
- **Captures:** Linear projection of `performance_logs` trend; later, more sophisticated models.
- **Decision:** Hiring, expansion, marketing spend.
- **Dependencies:** None.

### 5.3 Benchmark comparisons (L, future)
- **Problem:** "Is $165/session normal for my market?" Nobody knows.
- **Captures:** Anonymized peer data once enough practices are on the platform.
- **Decision:** Pricing, hiring, marketing investment.
- **Dependencies:** Requires sufficient scale to anonymize. Pure SaaS moat play.

---

## Theme 6 — Form builder module (NEW)

A single form-builder primitive that powers both client intake and clinician
onboarding. This is a strategic foundation, not a feature — it unlocks several
downstream capabilities and creates a sticky workflow surface.

### 6.1 Form builder core (L)
- **Problem:** Every practice has bespoke intake forms (HIPAA, history, consent, demographics, presenting concerns). Today these are PDFs or third-party tools (JotForm, etc.). Building this in-house means we own the data.
- **Captures:**
  - Form schemas (versioned)
  - Field types: short text, long text, single-select, multi-select, scale (1–10), date, signature, file upload, conditional logic ("show if X")
  - Required vs optional flags
  - Per-field metadata for AI prompts (e.g. "this field is the primary issue")
- **UX:** Drag-and-drop builder with a live preview pane. Templates library (default intake, biopsychosocial, PHQ-9, GAD-7, consent, etc.).
- **Decision:** Practices can stop paying for JotForm/Google Forms and keep data in one place.
- **Dependencies:** None — foundational module.
- **Reusability:** Same module powers intake forms, clinician onboarding, satisfaction surveys, outcome measures (PHQ-9 over time), and consent flows.

### 6.2 Client intake forms (M, after 6.1)
- **Problem:** Intake friction is real — clients abandon forms that are too long or feel impersonal. Owners need the data structured.
- **Captures:** Form responses linked to a client record; per-field analytics on completion / drop-off; "saved progress" for long forms.
- **Decision:** Which intake questions cause the most drop-off; how to shorten without losing signal.
- **Dependencies:** 6.1.

### 6.3 Clinician onboarding form + AI profile generator (M, after 6.1) ★
This is the high-value differentiator. When a clinician joins a practice, they
fill out a structured form that captures:
- Bio basics (name, license type, NPI, etc.)
- **Issues / presenting concerns they treat** (multi-select from a controlled list: anxiety, depression, trauma, OCD, ADHD, eating disorders, etc.)
- **Populations they serve** (multi-select: adolescents, couples, LGBTQIA+, BIPOC, perinatal, veterans, etc.)
- **Modalities / approaches** (CBT, EMDR, IFS, ACT, psychodynamic, etc.)
- Insurance accepted
- Availability windows
- Fee structure
- **Forced primary specialty pick** — a single issue + single population they identify as their strongest area. This is what makes the AI output sharp instead of generic.

**AI-generated outputs:**
- **Clinician marketing profile** — short bio, long bio, "what to expect" copy, FAQ block, all tuned to the primary specialty. Output usable directly on the practice website.
- **SEO blog topic suggestions** — 10–15 blog post ideas the clinician (or owner) could write to attract their ideal client (e.g. "EMDR for first responders: what to expect in your first three sessions").
- **Practice owner brief** — for the owner, a strategic summary: "Casey's primary lever is perinatal anxiety — your local SEO has weak coverage there. Three Psychology Today directory tweaks could double her inquiries."
- **Referral pitch generator** — short outreach copy for referral partners (PCPs, OB-GYNs, schools) tailored to the primary specialty.
- **Internal placement recommendations** — when new client inquiries come in, suggest the best-fit clinician based on stated needs and clinician profiles.

- **Decision:** Hiring decisions feed directly into marketing strategy; matching becomes data-driven instead of vibes-based.
- **Dependencies:** 6.1; later integrates with referral attribution (2.2) and lead capture (2.1) for full inquiry-to-clinician routing.
- **Strategic note:** This single feature lets us position the product as more than analytics — it becomes a *growth engine* for individual clinicians. Strong wedge for solo practitioners specifically.

### 6.4 Outcome measures (PHQ-9, GAD-7, etc.) (S, after 6.1)
- **Problem:** Insurance increasingly requires outcome tracking; clinicians want it for clinical reasons.
- **Captures:** Recurring assessments (weekly/monthly) administered through the form module, scored automatically, trended on the client record.
- **Decision:** Clinical: is this client improving? Business: which clinicians produce measurable outcomes?
- **Dependencies:** 6.1; client records.

### 6.5 Satisfaction surveys / NPS (S, after 6.1)
- **Problem:** Owners don't systematically collect client feedback.
- **Captures:** Post-session or quarterly surveys via the form module.
- **Decision:** Identify dissatisfaction before churn; surface testimonial-quality quotes.
- **Dependencies:** 6.1.

---

## Theme 7 — AI layer (cross-cutting)

The dashboard prototype assumes an AI layer. These are the specific capabilities
it implies, treated as a stack rather than individual features.

### 7.1 Daily briefing generator (M)
- **What it does:** Synthesizes the practice's current state into a 1-paragraph headline + 3 prioritized actions.
- **Inputs:** All KPIs, deltas, anomalies, recent data.
- **Output:** Narrative + structured priorities with impact estimates and confidence scores.
- **Build approach:** Claude API call with a structured prompt, cached daily.

### 7.2 Recommendation engine (M)
- **What it does:** Generates specific, actionable suggestions with $ impact and confidence.
- **Inputs:** Same as 7.1 plus historical patterns ("Friday afternoons have higher no-show").
- **Output:** Cards with category, action, projected outcome, confidence.

### 7.3 Anomaly detection (S–M)
- **What it does:** Surfaces unusual changes worth investigating (no-show rate doubled, collection rate dropped).
- **Approach:** Mostly rule-based / statistical to start; AI for narrative explanation. No need to over-engineer this.

### 7.4 Clinician profile generator (S, requires 6.3)
- See 6.3.

### 7.5 Re-engagement message drafter (S)
- **What it does:** For each at-risk client, drafts a personalized re-engagement message the owner or clinician can edit and send.
- **Inputs:** Client name, last session date, presenting concern, last clinical note (if accessible).

### 7.6 Blog / SEO content generator (S, requires 6.3)
- See 6.3.

---

## Theme 8 — Operational stickiness (lower priority but worth flagging)

### 8.1 EHR-lite (L, far future)
- Notes, treatment plans, intake history. The thing that locks practices in. Big build.

### 8.2 Document storage / SOP library (S)
- Onboarding playbooks, fee policies, intake scripts. Cheap stickiness.

### 8.3 Tax / retirement contribution planner (S)
- Quarterly estimated taxes, SEP-IRA / Solo 401k contribution suggestions.

### 8.4 Multi-location / multi-entity roll-up (M)
- Group practices with multiple LLCs or locations want both consolidated and per-location views.

### 8.5 Integrations (M each)
- Google Sheets import for performance logs
- Stripe for payment + collection tracking
- Google Calendar / iCloud sync for appointments
- EHR integrations (SimplePractice, TherapyNotes) — read-only at first

---

## Theme 9 — HR & people management

Owners of group practices spend a surprising amount of time managing
clinicians. This is one of the most underserved areas in the SimplePractice-
class of tools.

### 9.1 Hiring pipeline (M)
- **Problem:** Owners juggle applicants in spreadsheets or email threads. Loses candidates, drags decisions out.
- **Captures:** Applicants → screened → interviewed → offer → onboarding states; resume / CV upload; notes; rejection reasons.
- **Decision:** Who to move forward, who to drop, where the funnel is leaking.
- **Dependencies:** None. Tight integration with 6.3 (clinician onboarding form) once hired.

### 9.2 Supervision hours tracking (S) ★
- **Problem:** Associate-level clinicians (LPC-A, LMSW-A, LMFT-A, etc.) require thousands of supervised hours for full licensure. Tracked badly almost everywhere, often on paper. Failing this paperwork can delay full licensure by months.
- **Captures:** Per-clinician supervision log: date, supervisor, hours (individual vs. group), topics, signatures.
- **Decision:** Are we on track for each associate's licensure goal? Where are the gaps?
- **Dependencies:** None.
- **Stickiness note:** Once entered, owners will not migrate this. Several years of records build up.

### 9.3 1:1 / supervision meeting workspace (S)
- **Problem:** Owners do weekly 1:1s with each clinician but agendas live in Notion / paper / nowhere. Action items lost between meetings.
- **Captures:** Recurring 1:1 records with shared agendas, action items that carry forward, mood/check-in signals over time.
- **Decision:** Spot retention risks early; track development conversations.
- **Dependencies:** None.

### 9.4 PTO / time-off requests (S)
- **Problem:** Time-off requests come via Slack / text, easy to lose, hard to plan capacity around.
- **Captures:** Request → approve workflow; PTO balances; integration with availability so the schedule auto-updates.
- **Decision:** Approve / deny; capacity planning during predictable gaps (summer, holidays).
- **Dependencies:** Existing Availability model.

### 9.5 Continuing education tracking (S)
- **Problem:** Each license type requires X CE hours per renewal cycle. Tracked badly.
- **Captures:** Per-clinician CE log with topic, provider, hours, certificate upload, deadline alerts.
- **Decision:** Who's behind on CEs; CE budget allocation.
- **Dependencies:** Light overlap with credentialing tracker (3.2) — could share infrastructure.

### 9.6 Performance review cycles (M)
- **Problem:** Annual or semi-annual reviews are inconsistent / skipped under pressure.
- **Captures:** Review templates, self-review + manager review, goal-setting linked to outcome data (utilization, retention, no-show rate).
- **Decision:** Comp decisions, role expansion, development plans, parting ways.
- **Dependencies:** Per-clinician P&L (4.1) and outcome data ideally available.

### 9.7 Clinician handbook / policies (S)
- **Problem:** New hires get a PDF from email, nobody knows the current version.
- **Captures:** Versioned policy library with read-receipts.
- **Decision:** Compliance proof for audits.
- **Dependencies:** Doc storage (8.2).

---

## Theme 10 — Marketing operations (practice-level)

The clinician profile generator (6.3) handles per-clinician marketing.
This theme covers practice-level marketing workflows.

### 10.1 Referral partner CRM (M) ★
- **Problem:** PCPs, OB-GYNs, schools, EAPs, attorneys — these relationships drive the most durable referrals but live in someone's contacts list. Owners don't systematically nurture them.
- **Captures:** Referral partner records, last-contact dates, referral volume by partner, gifts / lunches / events tracking.
- **Decision:** Who to follow up with this month; which partner relationships are decaying; which produced the most clients.
- **Dependencies:** Referral attribution (2.2) for the volume metric.
- **Strategic note:** Referrals from a trusted PCP convert at ~70%+ and have 3x the LTV of paid ads. The ROI of this single feature for established practices is enormous.

### 10.2 Review collection workflow (S) ★
- **Problem:** Google reviews are the single highest-ROI marketing move for local healthcare. Most practices know this and still don't ask for them, because (a) it's awkward and (b) timing is everything.
- **Captures:** Triggered prompts after Nth session (or post-discharge), AI-drafted personalized request, one-click "send via email/SMS", response tracking.
- **Decision:** Which clinicians have the strongest review base; which need more.
- **Dependencies:** None for the workflow; integrates with Google Business Profile API later.

### 10.3 Google Business Profile management (M)
- **Problem:** GBP is the most important local SEO surface and most practices set it up once and never touch it.
- **Captures:** Posts, photos, hours, Q&A pulled into the app; AI-suggested weekly posts; review monitoring with AI-drafted responses (HIPAA-careful).
- **Decision:** What to post this week; how to respond to a negative review.
- **Dependencies:** GBP API integration; AI layer.

### 10.4 Content calendar + AI blog drafting (M)
- **Problem:** Owners know they should blog but never do. The blank page is the enemy.
- **Captures:** Content calendar tied to clinician specialties (from 6.3), AI-drafted blog posts (clinician reviews + edits), SEO keyword targeting.
- **Decision:** What to publish; who to assign it to.
- **Dependencies:** 6.3 (specialty data), AI layer.

### 10.5 Website CMS hooks (M)
- **Problem:** Clinician profile pages get stale; owners need to log in to WordPress.
- **Captures:** API endpoints + a small JS embed that pulls the AI-generated clinician profile, current openings, and reviews directly onto the practice website.
- **Decision:** Eliminates "is the website current?" as a recurring task.
- **Dependencies:** 6.3.
- **Strategic note:** Headless CMS-style — the practice website becomes a live reflection of the system, which is itself a wedge.

### 10.6 Psychology Today / directory sync (M)
- **Problem:** Owners maintain Psychology Today, Zocdoc, Headway, etc. profiles separately. Updating one means updating four.
- **Captures:** Source-of-truth profile in our system; directory-specific export/sync where APIs allow.
- **Decision:** "Update once, propagate everywhere."
- **Dependencies:** 6.3.

### 10.7 Email newsletter / segmented broadcasts (S)
- **Problem:** Many practices have a mailing list buried in Mailchimp; cadence is sporadic.
- **Captures:** Consent-managed list, segments (former clients, referral partners, prospects), HIPAA-safe templates.
- **Decision:** Reactivation campaigns, partner updates, content distribution.
- **Dependencies:** Consent management overlay (legal/regulatory).

---

## Theme 11 — Insurance operations

This is the operational layer below the analytics in Theme 1. Practices that
don't outsource billing spend hours per week on these tasks.

### 11.1 Eligibility & benefits verification (M)
- **Problem:** Before a client's first session, someone calls the insurance company to verify benefits. Eats 15–30 minutes per new client.
- **Captures:** Per-client benefits snapshot (deductible, copay, sessions allowed, in-network status).
- **Decision:** What to charge the client; whether to proceed without prior auth.
- **Dependencies:** Payer tracking (1.1); eventually integrations with eligibility APIs (Change Healthcare, etc.).

### 11.2 Pre-authorization tracking (S)
- **Problem:** Some payers require pre-auth for ongoing therapy. Owners lose money when sessions exceed authorized counts and weren't re-authed in time.
- **Captures:** Auth records (start, end, # sessions, current usage), alerts when 80% used.
- **Decision:** When to renew auths.
- **Dependencies:** Per-appointment payer linkage.

### 11.3 Claim status & denials (M)
- **Problem:** Submitted claims sit in limbo. Denials require appeals. Most owners track this in their EHR's awkward queue.
- **Captures:** Claim records (submitted / paid / denied / appealed), denial reasons, appeal templates.
- **Decision:** Which claims to appeal; which to write off; which payer is generating the most denials.
- **Dependencies:** Payer tracking (1.1); EHR integration eventually.

### 11.4 Statement generation & client invoicing (S)
- **Problem:** Self-pay clients often want monthly statements. Sliding-scale clients especially.
- **Captures:** Statement templates, auto-generated monthly, sent via email or downloaded.
- **Decision:** Reduces the back-and-forth ("can you send me a statement?").
- **Dependencies:** Per-appointment billed amount data.

### 11.5 Late-payment collections (S)
- **Problem:** Awkward for clinicians to ask. Often delayed for months. Cash flow killer.
- **Captures:** Automatic reminder cadence (Day 30 / 45 / 60), AI-drafted templated messages with appropriate tone, integration with payment processor.
- **Decision:** When to escalate; when to write off.
- **Dependencies:** Payment processor integration.

---

## Theme 12 — Legal, regulatory & risk

The "things that scare you awake at 3 am" category. Building these well is
genuinely de-risking the business — which is something owners will pay for.

### 12.1 Telehealth state-licensure compliance (S) ★
- **Problem:** Clinicians can only legally practice in states where they're licensed *at the time of session*, based on the client's physical location. Most practices manage this in their heads. A client traveling to another state and having a session is a real liability.
- **Captures:** Per-clinician licensed states; per-session client location confirmation (a pre-session check-in question); auto-block / warn if mismatch.
- **Decision:** Prevents a category of legal risk that's growing as telehealth grows.
- **Dependencies:** None. Mostly schema + a small pre-session workflow.
- **Strategic note:** This is a sleeper "must-have." Once a practice gets bitten by this, they will never trust a tool that doesn't have it.

### 12.2 BAA (Business Associate Agreement) tracking (S)
- **Problem:** Every HIPAA-relevant vendor needs a BAA. Most practices have a vague list in a drawer.
- **Captures:** Vendor records with BAA status, expiration, document upload, renewal reminders.
- **Decision:** Compliance proof; vendor onboarding diligence.
- **Dependencies:** None.

### 12.3 Subpoena / records request workflow (S)
- **Problem:** Records requests (subpoenas, court orders, client requests under HIPAA right-of-access) come in periodically and eat days of admin time. The legal framework is fixed but the workflow varies every time.
- **Captures:** Request intake → triage (legitimate? what's required?) → preparation → delivery → log. Built-in templates for common scenarios.
- **Decision:** Respond correctly and on time; document for legal protection.
- **Dependencies:** Document storage (8.2).

### 12.4 Crisis protocol library (S)
- **Problem:** Every practice has crisis protocols (suicide risk, abuse reporting, dual relationships) but they live in a binder.
- **Captures:** Searchable, versioned protocols accessible during a session; decision-tree style.
- **Decision:** Faster, more confident clinician response in crisis.
- **Dependencies:** Doc storage.

### 12.5 HIPAA training & attestation (S)
- **Problem:** Annual HIPAA training is required; tracking who completed when is messy.
- **Captures:** Training records, completion attestations, audit-ready exports.
- **Decision:** Audit readiness.
- **Dependencies:** Forms module (6.1) for the training quiz; doc storage.

### 12.6 Audit-ready export bundle (S)
- **Problem:** When a HIPAA / payer audit hits, owners scramble to compile records.
- **Captures:** One-click export bundle: policies, training records, BAAs, risk assessments, breach log.
- **Decision:** Audit response time goes from days to minutes.
- **Dependencies:** All the above.

---

## Theme 13 — Client communication & engagement

Standard SaaS features that don't differentiate on their own, but their
absence is a churn risk vs. SimplePractice / TherapyNotes.

### 13.1 Appointment reminders (S)
- **Problem:** Reminders reduce no-shows 30–50%. Table stakes.
- **Captures:** Configurable cadence (24h email + 2h SMS, etc.), per-client opt-in.
- **Decision:** Reduces no-show rate measurably.
- **Dependencies:** SMS / email provider integration.

### 13.2 Secure two-way messaging (M)
- **Problem:** Clinicians get text-messaged about scheduling, clinical concerns, etc. on personal phones — not HIPAA-safe.
- **Captures:** Per-client secure thread; encryption; auto-archive for clinical record.
- **Decision:** Clinician boundaries + compliance.
- **Dependencies:** Auth + encryption layer.

### 13.3 Client portal (M)
- **Problem:** Clients want to see upcoming appointments, pay balances, fill out forms, download statements without emailing.
- **Captures:** Read-only-ish portal for the client side.
- **Decision:** Reduces admin tickets; modern client experience.
- **Dependencies:** Form module (6.1), payment processor.

### 13.4 Self-scheduling (M)
- **Problem:** "Can we find a time" emails eat hours. Calendly-style flows convert better.
- **Captures:** Public booking page per clinician, honoring availability + insurance + specialty rules.
- **Decision:** Conversion from inquiry to booked.
- **Dependencies:** Existing availability + event types (already in app).

### 13.5 Group therapy enrollment (S)
- **Problem:** Running groups requires waitlist + enrollment + screening + recurring billing — usually done in a spreadsheet.
- **Captures:** Group definitions, enrollment workflow, attendance tracking.
- **Decision:** Whether to run more groups (high-margin format).
- **Dependencies:** Appointments, forms (for screening).

---

## Theme 14 — Financial admin / bookkeeping

The day-to-day money work, distinct from the strategic finance in Theme 1.

### 14.1 Expense tracking + receipt capture (S)
- **Problem:** Owners pay $20/mo for Expensify or pile receipts in a shoebox.
- **Captures:** Photo upload, OCR'd line items, category, vendor.
- **Decision:** Year-end tax prep is 80% less painful.
- **Dependencies:** OCR provider (cheap).

### 14.2 Vendor management (S)
- **Problem:** Rent, utilities, EHR, payroll, supervision, malpractice — all the recurring spends. Owners can't quickly answer "what do we spend on tools?"
- **Captures:** Vendors with category, monthly cost, renewal date, contract upload.
- **Decision:** Negotiation leverage; cuts during downturns.
- **Dependencies:** None.

### 14.3 1099 / W-2 generation prep (S)
- **Problem:** Year-end: which clinicians need 1099s, which need W-2s, totals per person. Owners often scramble.
- **Captures:** Tax form classification per clinician, total compensation rollup.
- **Decision:** Hand-off package to bookkeeper / tax pro is a 10-minute export.
- **Dependencies:** Per-clinician compensation tracking (4.1).

### 14.4 Bookkeeper handoff bundle (S)
- **Problem:** Bookkeepers ask for QuickBooks data, statements, expense logs, etc. Owners produce these manually each month.
- **Captures:** Auto-generated monthly bundle: revenue (collected) by category, expenses by category, AR snapshot, exportable to CSV / QuickBooks-compatible format.
- **Decision:** Eliminates 2–4 hours/month of owner admin.
- **Dependencies:** Most other financial tracking features.

### 14.5 Pro bono / sliding scale tracking (S)
- **Problem:** Many owners pledge X% to pro bono or sliding scale but can't tell if they're hitting it.
- **Captures:** Per-appointment "fee category" tag (full-fee, sliding, pro bono), with target % goal.
- **Decision:** Mission accountability; tax-relevant in some cases.
- **Dependencies:** None.

---

## Theme 15 — Group practice & partnership dynamics

For practices with multiple owners — a small but high-value segment.

### 15.1 Partner profit splits (S)
- **Problem:** Multi-owner practices need clean monthly distributions based on whatever formula they agreed on (equal, contribution-weighted, etc.).
- **Captures:** Partner records, split formula, monthly calculation + ledger.
- **Decision:** Distributions, partnership health.
- **Dependencies:** Financial data.

### 15.2 Decision / approval workflows (S)
- **Problem:** "Did we agree to hire?" "Did we approve that expense?" Lost in Slack threads.
- **Captures:** Decisions log with proposer, voters, outcomes, dates.
- **Decision:** Governance clarity; reduced partner friction.
- **Dependencies:** None.

### 15.3 Equity / buyout scenarios (M, future)
- **Problem:** Practices growing toward partnership tracks (associate → partner) or contemplating buyouts have no clean model.
- **Captures:** Cap table-lite, valuation calculators, vesting tracking.
- **Decision:** Major strategic moves with proper data.
- **Dependencies:** Mature financial data.

---

## Theme 16 — Strategic planning & annual cycles

### 16.1 Quarterly business review (QBR) generator (S)
- **Problem:** Owners know they should do QBRs but rarely make the time. Blank-page problem again.
- **Captures:** AI-generated QBR deck from existing dashboard data: what changed, what worked, what to do next quarter.
- **Decision:** Forces an actual quarterly strategy conversation.
- **Dependencies:** Dashboard data, AI layer.

### 16.2 Annual budget + plan (M)
- **Problem:** Most owners don't budget — they react to the bank balance. A simple budget vs. actual view changes that.
- **Captures:** Annual revenue + expense plan by category, monthly variance tracking.
- **Decision:** Should we hire, raise rates, cut a vendor?
- **Dependencies:** Financial data, goal tracking (5.1).

### 16.3 Pricing analysis (S)
- **Problem:** "When did we last raise rates?" Owners don't track this. They under-charge for years.
- **Captures:** Rate history per service / payer, market benchmark when available (5.3), one-click "raise all private-pay rates by X%" tool.
- **Decision:** Rate increase timing and magnitude.
- **Dependencies:** None.

### 16.4 Succession / exit planning (L, far future)
- **Problem:** Many owners want to sell or transition someday but have no plan.
- **Captures:** Documentation of systems, dependencies, key relationships, financial trends — packaged as a "sellable practice" portfolio.
- **Decision:** Increases practice valuation; reduces key-person risk.
- **Dependencies:** Long-tenured data across all other themes.

---

## Theme 17 — Owner accountability & focus

Speculative but possibly the most strategically interesting theme. Practice
owners are operators, not executives — they don't have a CEO coach. AI can
fill some of that role.

### 17.1 Decision journal (S) ★
- **Problem:** Owners make 50 decisions a month and never review them. They don't learn from outcomes.
- **Captures:** Lightweight log: decision, reasoning, expected outcome, review date. AI surfaces past decisions for review.
- **Decision:** Better decision-making over time.
- **Dependencies:** None.
- **Strategic note:** This is meta but powerful. Pair it with the AI layer and you have a "Chief of Staff for solo founders" angle.

### 17.2 Weekly accountability check-in (S)
- **Problem:** Owners commit to changes (raise rates by April, hire by June) and never follow through. No one's holding them accountable.
- **Captures:** AI weekly check-in: "You said you'd do X by Y. Status?" Surfaces drift before it's expensive.
- **Decision:** Closing the gap between strategy and execution.
- **Dependencies:** AI layer.

### 17.3 Owner wellbeing pulse (S)
- **Problem:** Therapist-owners burn out at high rates — ironic given the field. Owners don't track their own bandwidth.
- **Captures:** Weekly 30-second check-in (energy, sleep, hours worked, satisfaction). Trended privately.
- **Decision:** Self-awareness; early-warning for burnout.
- **Dependencies:** None.

### 17.4 Focus / "this week" board (S)
- **Problem:** Owners face decision fatigue. "I have 47 things I could do — what should I focus on?"
- **Captures:** AI-curated weekly focus list pulled from recommendations, anomalies, decision journal, and pending actions.
- **Decision:** A single screen that answers "what should I do next?"
- **Dependencies:** Most other features.

---

## Theme 18 — Integrations & platform edges

In addition to the integrations listed in 8.5:

### 18.1 EHR bridge (read-only sync) (M)
- **Problem:** Most practices won't leave SimplePractice / TherapyNotes for clinical records, but they would adopt a layer on top for BI + practice management.
- **Captures:** One-way sync of appointments, demographics, fee-per-session from supported EHRs.
- **Decision:** Lets us be the BI / strategy layer without forcing a migration.
- **Strategic note:** This is the "we're a layer, not a replacement" play. Probably the fastest path to early adopters.

### 18.2 Payment processor (Stripe / Square) (S–M)
- **Problem:** Payment data lives in Stripe; payment failures cause silent revenue loss.
- **Captures:** Real-time payment status, failed-payment alerts, ACH for lower fees.
- **Decision:** Reduces ~2–3% revenue leakage from failed cards.

### 18.3 Telehealth platform integration (S)
- **Problem:** Owners use Zoom / Doxy / SimplePractice telehealth. Login chaos.
- **Captures:** One-click session launch from the appointment; auto-generated room links.
- **Decision:** Lower friction = lower no-show rate.

### 18.4 Calendar bidirectional sync (S)
- **Problem:** Clinicians live in Google Calendar / iCal. Conflicts between personal calendar and the practice schedule cause double-booking.
- **Captures:** Two-way sync with conflict detection.

---

## Theme 19 — Clinical operations & documentation

The clinical work itself — notes, treatment plans, supervision sign-offs. This
is where companies like Mentalyc, Upheal, Eleos, and Heidi are eating share
right now with AI. We can't ignore it. A practice owner deciding between us
and SimplePractice will weigh clinical workflow heavily.

### 19.1 AI clinical scribe / note assistant (M) ★
- **Problem:** Clinicians spend 1–2 hours/day on documentation. Most are behind. Owners lose billable hours and quality of life suffers.
- **Captures:** With consent, record session audio → AI generates SOAP/DAP/BIRP note draft → clinician reviews and finalizes. Should match each payer's documentation requirements.
- **Decision:** Time savings = more sessions or more sanity. Documentation compliance reduces audit risk.
- **Dependencies:** Telehealth integration (18.3) for the easy case; in-person needs mic input. Strong consent flow + BAA with audio vendor.
- **Strategic note:** This is the single biggest AI play in mental health right now. If we ship it well, it's the wedge that pulls clinicians into the rest of the suite.

### 19.2 Treatment plan builder (S)
- **Problem:** Treatment plans are required by most payers (Medicare especially) but tedious to write. Often boilerplate that doesn't reflect actual care.
- **Captures:** Templated treatment plan structure (goals, objectives, interventions, expected duration) with AI assistance to draft from intake form + first 1–2 session notes.
- **Decision:** Compliance + quality. Reviewing AI drafts is faster than writing from scratch.
- **Dependencies:** Intake forms (6.2), notes from 19.1.

### 19.3 Co-signature workflow (S) ★
- **Problem:** Associate-level clinicians' notes need a licensed supervisor's signature within X days for billing compliance. This often slips, blocking claims.
- **Captures:** Pending-signature queue for each supervisor; deadline alerts; one-click sign-off with appropriate audit trail.
- **Decision:** Don't lose revenue to late sign-offs.
- **Dependencies:** Notes/documentation must live in the system (so could be via AI scribe 19.1 or by importing from EHR via 18.1).

### 19.4 Documentation compliance checker (S)
- **Problem:** Notes that don't include required elements get claims denied. Different payers require different things (medical necessity language, time documented, intervention specifics).
- **Captures:** Per-payer rule set; AI flags notes that are likely to be denied before submission.
- **Decision:** Catch compliance gaps before they cost money.
- **Dependencies:** 19.1.

### 19.5 Diagnosis / DSM lookup + ICD-10 search (S)
- **Problem:** Clinicians often default to F41.1 (Generalized Anxiety) because it's easy. More accurate coding sometimes pays better and matches clinical reality.
- **Captures:** Searchable DSM-5-TR + ICD-10 with payer-coverage hints.
- **Decision:** Cleaner clinical data; better-fitting codes.
- **Dependencies:** None.

### 19.6 Session prep summarizer (S)
- **Problem:** Between sessions clinicians forget important context. "Wait, what did we talk about last time?" Often happens 5 minutes before session.
- **Captures:** AI-generated 30-second summary of the last few sessions, key themes, what client said they'd work on between sessions.
- **Decision:** Better continuity → better client experience → better retention.
- **Dependencies:** 19.1 or imported notes from 18.1.

---

## Theme 20 — Client safety & care continuity

High-stakes, low-frequency events. The features owners hope to never use but
need to bulletproof. Owning safety/risk is also a way to attract owners who
care about doing therapy well, not just running a business.

### 20.1 Standardized risk screening (S) ★
- **Problem:** Most practices screen for suicide / abuse risk only at intake. Risk fluctuates over time and needs reassessment, ideally at every session for elevated-risk clients.
- **Captures:** C-SSRS or similar screener via the form module (6.1); auto-administered on a schedule based on prior risk score; alerts when risk escalates.
- **Decision:** Clinical safety + legal protection. Documented risk monitoring is the standard of care.
- **Dependencies:** Form module (6.1).

### 20.2 Safety planning workspace (S)
- **Problem:** A safety plan is the evidence-based intervention for suicidal clients. Often handwritten and lost in a paper file.
- **Captures:** Structured safety plan template (warning signs, internal coping, social contacts, professional contacts, lethal means restriction), shareable with client.
- **Decision:** Right care at the right moment; legal-protective documentation.
- **Dependencies:** Forms.

### 20.3 Higher-level-of-care referral tracking (S)
- **Problem:** Sometimes outpatient isn't enough — client needs IOP/PHP/residential/inpatient. Owners need to track these referrals (referred out, accepted, returned to outpatient).
- **Captures:** HLOC referral records with destination, reason, dates, outcome.
- **Decision:** Quality-of-care tracking; sometimes a contractual requirement with referral partners.
- **Dependencies:** None.

### 20.4 Discharge planning workflow (S)
- **Problem:** Therapy endings are often unceremonious — client just stops scheduling. Best practice is intentional discharge with a summary and follow-up plan.
- **Captures:** Discharge note template, optional alumni follow-up scheduling.
- **Decision:** Outcome data; alumni who may return; clinical quality.
- **Dependencies:** None.

### 20.5 Clinician departure / client transition workflow (M) ★
- **Problem:** When a clinician leaves the practice, their 40+ active clients need to be transitioned. Doing this well is essential clinically and legally; doing it poorly loses clients to whoever the departing clinician refers them to.
- **Captures:** Transition workflow: client list with risk-stratified priority, matching to remaining clinicians using profile data (6.3), templated communication to clients, status tracking.
- **Decision:** Retain clients during turnover; legal/ethical compliance with abandonment standards.
- **Dependencies:** Clinician profiles (6.3) for matching.
- **Strategic note:** Owners feel acute pain about this when it happens — a single product moment can convert someone into a long-term customer.

### 20.6 Mandated reporting workflow (S)
- **Problem:** When child abuse, elder abuse, or imminent danger is suspected, clinicians must report within hours. Workflow is high-stakes and varies by state.
- **Captures:** State-specific reporting workflow with required documentation, contacts (CPS, APS), templates, log for legal protection.
- **Decision:** Right action quickly; documented compliance.
- **Dependencies:** None.

---

## Theme 21 — New client onboarding & first impressions

The first 14 days post-inquiry determine whether a client becomes a lifelong
patient or one of the 30% who drop out before session 4. Most practices wing
this. A systematic onboarding experience is a retention superpower.

### 21.1 New client welcome packet (S)
- **Problem:** New clients get a flurry of forms and emails from various places. Disjointed first impression.
- **Captures:** Branded welcome experience: intake forms, what-to-expect content, clinician video bio (from 6.3), payment setup, calendar — all in one flow.
- **Decision:** First impression matters disproportionately.
- **Dependencies:** Forms (6.1), client portal (13.3) eventually.

### 21.2 Clinician video bio (S) ★
- **Problem:** Clients say the #1 factor in choosing a clinician is "did I connect with how they introduced themselves." Most practices have static text bios.
- **Captures:** Recorded 60–90 second video bio per clinician (recorded once), embedded in directory + welcome packet + website (10.5).
- **Decision:** Higher inquiry-to-booking conversion.
- **Dependencies:** Clinician profiles (6.3).
- **Strategic note:** Cheap to build, immediately marketable, every solo practitioner will love it.

### 21.3 First-session no-show recovery workflow (S)
- **Problem:** First-session no-shows are 2–3× higher than ongoing-client no-shows. And they're high-stakes — that client is gone if you don't recover them.
- **Captures:** Automatic same-day recovery flow: AI-drafted "concerned, would love to reschedule" message; one-click rebook link.
- **Decision:** Recover otherwise-lost first-session clients.
- **Dependencies:** Reminders/messaging (13.1, 13.2).

### 21.4 New client checklist (S)
- **Problem:** Was that intake form returned? Was insurance verified? Was payment method captured? Multiple admin people, multiple gaps.
- **Captures:** Per-client onboarding checklist surfaced for admins; flags clients with missing pieces.
- **Decision:** Nothing falls through the cracks.
- **Dependencies:** Form completion tracking, eligibility (11.1).

### 21.5 Clinician matching quiz (M) ★
- **Problem:** Clients calling a group practice often don't know which clinician to ask for. Receptionist guesses; matches are often mediocre.
- **Captures:** Public-facing 60-second quiz ("what brings you in," "preferred modality," "insurance," "demographic prefs") that routes to best-fit clinician using profile data (6.3).
- **Decision:** Conversion + clinical fit. Removes the "well, who do I ask for?" friction.
- **Dependencies:** Clinician profiles (6.3).
- **Strategic note:** Doubles as a marketing asset — embed it on the practice website (10.5).

---

## Theme 22 — Specialty / niche practice workflows

Different specialties have meaningfully different operational needs. Building
features that handle these correctly removes the "this tool wasn't built for
me" friction.

### 22.1 Couples / family therapy support (M)
- **Problem:** Multiple clients per case. Dual consent (both partners must consent to release info). Shared notes vs. individual notes. Billing complications.
- **Captures:** Case-as-entity (multiple clients linked to one case), dual consent tracking, shared + private clinician notes, who-pays-what billing.
- **Decision:** Practices doing couples work can't operate well without this.
- **Dependencies:** Notes (19.1), forms (consent versions).

### 22.2 Group therapy operations (M)
- **Problem:** Groups have enrollment, screening, attendance, recurring billing, group notes — all messy without dedicated tooling.
- **Captures:** Group definitions (topic, schedule, capacity, fee), enrollment + screening workflow, attendance log, group-note templates.
- **Decision:** Groups are high-margin formats — making them easy to run grows revenue.
- **Dependencies:** Appointments, forms.

### 22.3 Addiction / 42 CFR Part 2 documentation (M)
- **Problem:** Substance use treatment records have stricter confidentiality requirements than HIPAA (42 CFR Part 2). Most non-specialized tools don't handle it.
- **Captures:** Higher-restriction record class for Part 2 clients, separate audit log, stricter consent management for any disclosure.
- **Decision:** Compliance for practices serving SUD populations; unlocks that segment.
- **Dependencies:** Records access controls.

### 22.4 Forensic / evaluation work (M)
- **Problem:** Forensic psychologists / evaluators have different workflows: court orders, evaluation reports, attorney communication, billing per evaluation (not per session).
- **Captures:** Evaluation case type with attorney/court party tracking, report templates, milestone-based billing.
- **Decision:** Tool fits this niche; usually a high-fee specialty.
- **Dependencies:** None.

### 22.5 Telepsychiatry / medication management (M)
- **Problem:** Psychiatric NPs and MDs have controlled substance compliance (DEA registration, state PMP checks, refill workflows). Lots of practices want to add a med-management arm.
- **Captures:** Med list per client, refill request workflow, PMP check log, prescription tracking. Probably integrates with e-prescribe vendors later.
- **Decision:** Multi-disciplinary practices can run psychiatry alongside therapy.
- **Dependencies:** Higher compliance bar; later phase.

### 22.6 Sports / performance / executive coaching workflows (S)
- **Problem:** Coaching-style practices have organization-as-client billing, different documentation expectations (often non-clinical), team-level engagement.
- **Captures:** Organization client type, team rosters, session attribution.
- **Decision:** Niche but valuable; coaching is a growth segment.
- **Dependencies:** None.

---

## Theme 23 — Client-side experience & between-session engagement

The therapy relationship is 50 weekly minutes; the rest is the client's life.
Tools that bridge that gap improve outcomes — and outcomes are the durable
moat against churn.

### 23.1 Homework / between-session exercises (S)
- **Problem:** Clinicians assign homework (worksheets, behavioral experiments, journaling). Clients lose the printouts; clinicians can't see if it was done.
- **Captures:** Assigned exercises with completion tracking; library of evidence-based worksheets (CBT, DBT, ACT).
- **Decision:** Clinical engagement; clinician can adjust based on actual homework data.
- **Dependencies:** Client portal (13.3).

### 23.2 Mood / symptom tracking (S)
- **Problem:** "How have you been since last session?" is the universal opener. Clients answer vaguely.
- **Captures:** Quick daily mood/symptom check-ins (1-minute form), trended chart available to client + clinician.
- **Decision:** Richer between-session data; better-targeted sessions.
- **Dependencies:** Forms (6.1), portal.

### 23.3 Between-session messaging / check-ins (S)
- **Problem:** Many clients want a structured check-in mid-week. Texting the clinician's personal phone isn't appropriate.
- **Captures:** Bounded async messaging window (e.g. one short check-in / week), clinician reads/responds on schedule.
- **Decision:** Adds clinical value without burning out clinicians.
- **Dependencies:** Secure messaging (13.2).

### 23.4 Client resource library (S)
- **Problem:** Clinicians recommend articles/books/apps/podcasts in session; client forgets which by Monday.
- **Captures:** Curated resource library + per-client recommendations from clinician.
- **Decision:** Higher-quality between-session experience.
- **Dependencies:** Client portal.

### 23.5 Outcome-informed care dashboards (S)
- **Problem:** Most therapy is "I think it's helping" — no measurement. Outcome-informed care is evidence-based and increasingly required by payers.
- **Captures:** Per-client outcome trend (from PHQ-9, GAD-7, ORS, etc. via 6.4), surfaced to both client and clinician.
- **Decision:** Demonstrate effectiveness; identify clients not improving for treatment-plan revision.
- **Dependencies:** Outcome measures (6.4).
- **Strategic note:** Marketing point: "Our practice uses outcome-informed care." Increasingly a credentialing requirement.

---

## Theme 24 — Owner personal finance & tax planning

The owner's personal finances are entangled with the practice. Most owners
have no clean model and pay an accountant $$$ to figure it out at year-end.

### 24.1 S-corp salary planning (S) ★
- **Problem:** Most therapists eventually elect S-corp tax status to save self-employment tax. The "reasonable salary" you pay yourself is a constant tension — too low and the IRS challenges, too high and you lose the tax benefit.
- **Captures:** Salary recommendation based on revenue, hours, region, plus distribution planning. Quarterly check-in: "Are you on track for your target salary/distribution mix?"
- **Decision:** Tax efficiency without IRS risk. Real money — often $5–15K/year of tax savings poorly executed.
- **Dependencies:** Owner-compensation tracking (1.3).

### 24.2 Retirement contribution planner (S)
- **Problem:** Solo 401k vs. SEP-IRA vs. Defined Benefit Plan — wildly different contribution limits ($25K vs. $69K vs. $300K+). Most owners pick once and never revisit.
- **Captures:** Annual contribution scenarios based on practice income; deadline tracking; coordination with S-corp salary (24.1).
- **Decision:** Six-figure decisions on tax-advantaged retirement.
- **Dependencies:** 24.1.

### 24.3 Quarterly estimated tax calculator (S)
- **Problem:** Owners under- or over-pay quarterly estimates and get hit with penalties or zero cash flow planning.
- **Captures:** Estimated quarterly tax projection based on YTD income + S-corp salary.
- **Decision:** Pay just enough on time.
- **Dependencies:** Financial data.

### 24.4 Owner health insurance / Section 125 planning (S)
- **Problem:** Health insurance for solo/family is expensive. S-corp owners face specific rules around how health insurance can be paid (must be on W-2 to be deductible properly).
- **Captures:** Health insurance line item with S-corp compliance check, HSA/FSA planning.
- **Decision:** Don't miss deductions; don't trip IRS rules.
- **Dependencies:** 24.1.

### 24.5 Mileage / vehicle deduction tracking (S)
- **Problem:** Clinicians doing home visits, school visits, or court appearances rack up business mileage they don't track.
- **Captures:** Mileage log (manual or GPS-assisted), purpose, tax-categorized export.
- **Decision:** Captures $1–3K/year in often-missed deductions.
- **Dependencies:** None.

### 24.6 Year-end tax close package (S)
- **Problem:** Year-end is a scramble. Accountant asks for 20 things.
- **Captures:** Auto-generated year-end package: P&L, balance sheet, 1099/W-2 totals, mileage, home office, vendor expenses, asset depreciation.
- **Decision:** Saves 4–8 hours of owner scramble; accountant bills less.
- **Dependencies:** Financial admin features (14.x).

---

## Theme 25 — Renewals & deadline calendar (consolidated)

Cross-cutting feature: there are dozens of things that expire across the
themes above (licenses, BAAs, malpractice, lease, contracts, payer
credentialing, auths, NPI, CEs). Owners track none of them well.

### 25.1 Unified renewal calendar (S) ★
- **Problem:** Each domain has its own expiration date. Owners get bitten by one or two per year. Lost revenue and compliance gaps.
- **Captures:** One consolidated view across all expiration-having entities (licenses, BAAs, malpractice, leases, contracts, payer credentialing, auths, NPI, CEs, mandated reporter training, HIPAA risk assessment, etc.) with multi-stage alerts (90d / 30d / 7d / overdue).
- **Decision:** Nothing lapses unintentionally.
- **Dependencies:** Pulls from all the date-bearing models — credentialing (3.2), CE (9.5), BAAs (12.2), vendor contracts (14.2), partnership agreements, etc.
- **Strategic note:** Tiny build effort once the underlying data exists. High gratitude per dollar of dev.

### 25.2 Insurance coverage checker (S)
- **Problem:** Malpractice, cyber liability, general liability, workers comp — overlapping coverage with gaps owners don't see.
- **Captures:** Active policies, coverage limits, deductibles, gap analysis.
- **Decision:** Right coverage at right cost.
- **Dependencies:** None.

---

## Theme 26 — Continuity planning (clinician departure, owner illness, crisis events)

Low-frequency, high-cost events that destroy unprepared practices.

### 26.1 Clinician departure runbook (S)
- See 20.5 — operationally executed via this runbook.
- **Captures:** Step-by-step playbook (notify clients, transition risk-stratified caseload, claw back system access, payer notification, etc.). AI-personalized to the departing clinician's caseload.
- **Decision:** Smooth transitions instead of chaos.
- **Dependencies:** 20.5.

### 26.2 Owner incapacity / continuity plan (S) ★
- **Problem:** If the owner dies or is hospitalized, what happens to the practice tomorrow? Most practices have no answer.
- **Captures:** Designated backup contact, access credentials in escrow (encrypted), client communication templates, emergency clinician coverage agreement.
- **Decision:** Loved ones and clients aren't stranded. Ethical obligation, also legal.
- **Dependencies:** Doc storage, secure access management.
- **Strategic note:** Sounds morbid but every owner over 40 with kids quietly worries about this. Selling it requires sensitivity.

### 26.3 Cybersecurity incident response (S)
- **Problem:** Ransomware and data breaches happen. HIPAA mandates specific response steps within tight timeframes.
- **Captures:** Documented incident response playbook (contain, assess, notify HHS/clients/state AG/media as required).
- **Decision:** Compliance + reputation in worst case.
- **Dependencies:** None.

### 26.4 Disaster recovery / business continuity (S)
- **Problem:** Office fire, flood, extended power outage. How do clinicians keep working?
- **Captures:** Documented recovery plan (where do sessions move to?), tested backups, payroll continuity.
- **Decision:** Resilience.
- **Dependencies:** None.

---

## Theme 27 — Internal collaboration & case consultation

For group practices, the team has to communicate clinically and operationally
without resorting to non-HIPAA-safe channels.

### 27.1 HIPAA-safe internal chat (M)
- **Problem:** Clinicians use Slack, text, or email for clinical discussions. Not HIPAA-safe.
- **Captures:** Encrypted internal chat with retention controls, audit log.
- **Decision:** Compliance + team communication.
- **Dependencies:** Auth/encryption.

### 27.2 Case consultation requests (S)
- **Problem:** "I have a case I'd like to consult on" — currently a Slack message or hallway conversation. No record.
- **Captures:** Structured consultation requests (client de-identified or referenced by ID), discussion thread, outcome tagged to client record.
- **Decision:** Clinical quality + documented use of consultation (a clinical-defensibility plus).
- **Dependencies:** Internal chat (27.1).

### 27.3 Treatment team meetings (S)
- **Problem:** Many group practices have weekly clinical team meetings. Agendas + minutes are in someone's notebook.
- **Captures:** Recurring meeting with agenda, attendance, cases discussed, action items.
- **Decision:** Continuity across weeks; documented for higher-LOC practices where required.
- **Dependencies:** None.

### 27.4 Anniversary / recognition / culture tracking (S)
- **Problem:** Group practice culture matters for retention. Small recognition moments (work anniversaries, certifications earned, big-win moments) get missed.
- **Captures:** Auto-flagged anniversaries, certification milestones, team-shoutout feed.
- **Decision:** Retention; team morale.
- **Dependencies:** None.

---

## Theme 28 — Practice expansion & service-line decisions

Once the practice is healthy, owners think about growth. Most growth decisions
get made on gut.

### 28.1 New service line ROI calculator (S)
- **Problem:** Owners think about adding group therapy, psychiatric med-mgmt, nutrition, EMDR services, etc. Without a model they over- or under-invest.
- **Captures:** Inputs (capacity, fee, expected utilization, startup cost) → year-1 and year-2 contribution projection.
- **Decision:** Add this service line, or not?
- **Dependencies:** Financial data.

### 28.2 New location / satellite office decision (M)
- **Problem:** Adding a second location is a major bet. Most owners do it on gut.
- **Captures:** Pro-forma model (rent, equipment, second-clinician hire, ramp curve, breakeven months) with comparable-region benchmarks.
- **Decision:** Expand, defer, or scrap.
- **Dependencies:** Financial data; benchmarks (5.3) if available.

### 28.3 Multi-state telehealth expansion planner (S)
- **Problem:** Telehealth lets you serve more states, but each adds licensing cost (~$200–500 + CE requirements + renewal cycle). Which states are worth it?
- **Captures:** Per-state license cost vs. estimated demand (referral data + market size), prioritized list.
- **Decision:** Where to license next.
- **Dependencies:** Telehealth state compliance (12.1).

### 28.4 Practice acquisition / M&A modeling (L, far future)
- **Problem:** Some owners buy other practices. Most have no framework for valuing one.
- **Captures:** Practice valuation calculator (revenue multiple, EBITDA, key-person risk, payer mix), due diligence checklist.
- **Decision:** Make/decline an offer; structure of deal.
- **Dependencies:** Mature financial data.

---

## Theme 29 — Inquiry response & lead conversion

Industry data: leads contacted within 5 minutes convert ~9× higher than leads
contacted within 30+ minutes. Most practices respond 24–48 hours later, often
from a generic inbox. The single biggest leverage point between "got inquiry"
and "client books" is hardly touched today.

### 29.1 Multi-channel inquiry intake (S) ★
- **Problem:** Inquiries come via website form, Psychology Today, phone, Google Business Profile, referral partner emails — and live in different inboxes. Easy to lose.
- **Captures:** Single unified inbox for all inquiry channels with source attribution.
- **Decision:** Nothing gets missed; response time becomes measurable.
- **Dependencies:** Lead capture (2.1) extended with channel plumbing.

### 29.2 5-minute SLA response engine (S) ★
- **Problem:** Even practices that want to respond fast can't sustain it during sessions or after hours.
- **Captures:** Auto-acknowledge within 5 minutes with AI-drafted personalized first response based on inquiry content (presenting concern, insurance, preferred clinician); flag stuck inquiries to a human.
- **Decision:** Response time SLA met; conversion lift.
- **Dependencies:** 29.1; AI layer.
- **Strategic note:** This single capability would beat 90% of practices' current process. Easy ROI story to tell.

### 29.3 Lead nurture sequences (S)
- **Problem:** Many inquiries fall into "not ready yet" — they researched, asked questions, then went quiet. Without nurture they're lost.
- **Captures:** Triggered email/SMS sequences (psychoeducation about therapy, what to expect, removing fear-of-the-unknown), respectful frequency, AI-personalized to inquiry's stated concern.
- **Decision:** Recover fence-sitters who would otherwise churn.
- **Dependencies:** 29.1.

### 29.4 Inquiry quality scoring (S)
- **Problem:** Not all leads are equal. Tire-kickers vs. serious clients look similar at first.
- **Captures:** AI scoring on inquiry content + source quality + response patterns; surfaces highest-likelihood-to-book first.
- **Decision:** Where to spend admin time when 10 inquiries come in the same day.
- **Dependencies:** Some inquiry history to train against.

### 29.5 Ad performance attribution (M)
- **Problem:** Google Ads / Facebook Ads spend goes in; clients come out — usually no connection. Owners optimize blindly.
- **Captures:** UTM/source pass-through from ad click → inquiry → booked client → LTV. Closed-loop reporting.
- **Decision:** Which campaigns to scale, kill, or rework.
- **Dependencies:** 29.1, payment/LTV data.

### 29.6 Phone call tracking (M)
- **Problem:** A meaningful share of inquiries come via phone. Owners can't tell which marketing source drove the call.
- **Captures:** Call tracking numbers per source, call recording (with consent) for AI summarization, missed-call follow-up.
- **Decision:** Closed-loop on phone-driven inquiries.
- **Dependencies:** Call tracking provider integration.

---

## Theme 30 — AI-powered analytics & natural language

Beyond the daily briefing in 7.1, this is the layer where AI becomes the
analytics interface itself.

### 30.1 Natural-language query (M) ★
- **Problem:** Owners have specific questions ("show me my worst week of Q1", "which clinician converts the most BCBS clients to private pay") that don't map to existing dashboard sections.
- **Captures:** Chat-style query interface that translates plain language to data queries and returns charts/tables/answers.
- **Decision:** Ad-hoc analysis without analyst skills.
- **Dependencies:** Strong data model.
- **Strategic note:** This is the future of analytics UI. Built well, it's a wedge against every analytics tool that requires a dashboard editor.

### 30.2 "What if" scenario planner (M)
- **Problem:** Owners agonize over decisions ("raise rates 10%? add a clinician? drop Aetna?"). Scenario modeling is mental, not numeric.
- **Captures:** Sliders / what-if controls on the dashboard that project P&L impact 6 / 12 months out using historical patterns.
- **Decision:** Make decisions with a model instead of a gut.
- **Dependencies:** Robust financial data.

### 30.3 Anomaly explanation (S)
- **Problem:** Dashboard says "profit margin dropped 8%" — owner thinks "why?"
- **Captures:** AI-generated narrative explaining the *driver* of any anomaly ("88% of the margin drop is explained by an increase in BCBS sessions from 22% to 38% of mix").
- **Decision:** Skip the diagnosis step; go straight to the response.
- **Dependencies:** Anomaly detection (7.3), causal data.

### 30.4 Cohort discovery (M)
- **Problem:** "Why do some clients stay 20 sessions and others 3?" Patterns aren't obvious; sometimes counter-intuitive (e.g. *higher* fee correlates with *better* retention).
- **Captures:** AI surfaces non-obvious cohort patterns in retention, conversion, no-shows.
- **Decision:** Identify levers that aren't visible without statistical eyes.
- **Dependencies:** Sufficient data volume.

### 30.5 Auto-generated quarterly narrative (S)
- **Problem:** Owners struggle to summarize the quarter to themselves, partners, or accountants. Numbers are there; the story isn't.
- **Captures:** AI generates a 2-page "state of the practice" doc each quarter (or month) covering what changed, why, and what's next.
- **Decision:** Communication artifact; forces owner reflection.
- **Dependencies:** Dashboard data, AI layer.

---

## Theme 31 — Specialized populations

Different client populations have meaningfully different operational and
clinical needs. Building these in is how we win each segment instead of being
"good enough for everyone."

### 31.1 Pediatric workflow (M) ★
- **Problem:** Therapy with minors involves parents (consent, payment, sometimes session participation), schools (IEP/504 coordination, teacher communication), and custody complexity. Most tools treat the minor as the client and ignore the surrounding system.
- **Captures:** Parent/guardian linkage with consent management, custody status tracking, school coordination workflow, parent-vs-clinician communication channels.
- **Decision:** Practices serving kids/teens stop fighting their software.
- **Dependencies:** Forms, messaging.
- **Strategic note:** Pediatric mental health is the fastest-growing specialty. Practices focused on kids are well-funded and frustrated with their current tools.

### 31.2 Veteran / military workflow (S)
- **Problem:** TRICARE rules, VA Community Care referrals, military culture competency markers, security clearance considerations.
- **Captures:** TRICARE payer setup, VA referral tracking, military-specific intake items.
- **Decision:** Practice can serve veteran clients well — a fast-growing segment with stable payment.

### 31.3 Geriatric workflow (S)
- **Problem:** POA documentation, capacity assessment, family involvement, Medicare specifics (different from commercial insurance).
- **Captures:** Power of attorney records, capacity status, Medicare payer setup.
- **Decision:** Practice can serve older adults appropriately.

### 31.4 LGBTQIA+ affirming care markers (S)
- **Problem:** Clinicians and practices want to signal affirming-care competency; clients want to find them. Currently a Psychology Today checkbox.
- **Captures:** Clinician markers (chosen name + pronoun training, gender-affirming care letters, ROGD-rejection stance, etc.), client preferences at intake, matching logic.
- **Decision:** Right-fit clinical placement; marketing differentiation.

### 31.5 EAP / Employer contract management (M)
- **Problem:** EAP (Employee Assistance Program) work has different billing (session cap per employee, bulk billing to employer), different reporting requirements (de-identified utilization to employer), different consent rules.
- **Captures:** EAP contract records, per-employer session tracking, de-identified utilization reports.
- **Decision:** Practice can sustainably do EAP work, often a high-volume reliable revenue stream.

### 31.6 University / college counseling contracts (S)
- **Problem:** University contracts (e.g., overflow from campus counseling) have similar dynamics to EAPs.
- **Captures:** Per-school contract, students-served tracking, semester-aligned reporting.
- **Decision:** Stable institutional revenue.

---

## Theme 32 — Billing transparency & No Surprises Act compliance

The No Surprises Act (effective 2022) requires healthcare providers to give
self-pay clients written **Good Faith Estimates** before service. Enforcement
has been light but is real — and most practices either don't comply, comply
manually, or use clunky templates.

### 32.1 Good Faith Estimate generation (S) ★
- **Problem:** Legally required; most practices ignore or do badly.
- **Captures:** Auto-generated GFE per self-pay client with estimated cost, services, and dispute-rights language; archived to client record.
- **Decision:** Legal compliance + transparency.
- **Dependencies:** Fee structure data.
- **Strategic note:** Compliance hook into a category of feature owners didn't know they needed until you tell them. "By the way, are you doing GFEs?" is a great sales opener.

### 32.2 Cost estimator at booking (S)
- **Problem:** "How much will this cost me?" is the #1 client question; most practices answer poorly.
- **Captures:** Pre-booking estimate based on payer (if known), deductible status, fee schedule.
- **Decision:** Reduces no-shows from sticker shock; transparency increases trust.
- **Dependencies:** Payer data (1.1), eligibility (11.1).

### 32.3 Out-of-network reimbursement guide (S)
- **Problem:** Many private-pay practices have clients with OON benefits who don't realize it. Practices that help clients access these benefits convert better.
- **Captures:** Templated reimbursement guide per major payer (how to file a superbill); auto-generated superbills.
- **Decision:** Convert insurance-curious clients into private-pay clients.
- **Dependencies:** Statement/invoicing (11.4).

### 32.4 Network status disclosure tracking (S)
- **Problem:** "Are you in-network with my insurance?" — accurate answers require knowing current status with each payer (which changes more than people realize).
- **Captures:** Per-payer per-clinician network status with effective dates; surfaces during inquiry/booking.
- **Decision:** Transparency at the first conversation.
- **Dependencies:** Credentialing tracker (3.2).

---

## Theme 33 — Mobile experience

The product so far is web-only. Clinicians and clients increasingly expect
mobile. This isn't a single feature — it's a posture toward what to build
mobile-first vs. mobile-supported.

### 33.1 Clinician mobile app (M)
- **Problem:** Clinicians want to write notes between sessions, check tomorrow's schedule, respond to messages — often from their phone.
- **Captures:** Native or PWA mobile experience scoped to clinician tasks (schedule view, notes, messaging, quick stats).
- **Decision:** Faster documentation; better work-life integration.

### 33.2 Client mobile app (M)
- **Problem:** Clients do homework, check appointments, send a message, fill out outcome measures — phone-native.
- **Captures:** Client-facing mobile experience: forms, mood tracking, messaging, scheduling.
- **Decision:** Engagement; modern client experience.

### 33.3 Push notifications (S)
- **Problem:** Email reminders get ignored; SMS works but costs money. Push is free + reliable.
- **Captures:** Push for both clinicians (urgent message, no-show alert) and clients (session reminder, homework reminder, mood check-in).
- **Decision:** Lower friction + lower SMS cost.

### 33.4 Offline mode (S–M)
- **Problem:** Spotty connectivity for clinicians doing home visits, school visits, court work.
- **Captures:** Notes drafted offline sync when connection returns.
- **Decision:** Work happens regardless of connectivity.

---

## Theme 34 — Workflow automation (power-user)

For practices that grow into this — "if this then that" for practice operations.

### 34.1 Triggered automations (M) ★
- **Problem:** Owners have repeated workflows ("when a new client books, do A, B, C, D") that today are checklists in someone's head.
- **Captures:** Visual rule builder: trigger (new booking, payment failed, no-show, etc.) → conditions → actions (send message, assign task, create record).
- **Decision:** Practice runs itself for routine cases; humans intervene on exceptions.
- **Dependencies:** Most of the underlying entities need to exist.

### 34.2 Bulk operations (S)
- **Problem:** "Raise all private-pay rates 5%", "send year-end statements to everyone", "deactivate all clients with no visit in 6 months" — these are manual one-by-one today.
- **Captures:** Filterable client/clinician/appointment lists with bulk-action toolbar.
- **Decision:** 10 minutes instead of 10 hours.

### 34.3 Template / playbook library (S)
- **Problem:** Owners reinvent message templates, intake forms, treatment plans across practices. A shared library of community-vetted templates is valuable.
- **Captures:** Sharable templates with attribution; in-app library with copy-and-customize.
- **Decision:** Faster setup; better-than-average defaults.

---

## How to use this doc for project planning

When prioritizing, weigh each idea against:

1. **Decision value** — how often does this drive an actual owner decision?
2. **Build cost** — S / M / L
3. **Dependencies** — does anything need to exist first?
4. **Stickiness** — does it create switching cost?
5. **Differentiation** — does it make us hard to replace with SimplePractice + a spreadsheet?

Highest-leverage near-term picks (subjective, open for debate):

- **3.1 No-show tracking** — schema add, huge insight payoff
- **5.1 Goal tracking + variance** — reuses existing data, drives weekly engagement
- **2.4 At-risk client detection** — small compute, immediate value
- **6.1 + 6.3 Form builder + AI clinician profile** — strategic foundation that opens up marketing, intake, and outcome measures all at once

After those, the obvious follow-ons are **1.1 Payer tracking** → **1.2 AR aging** → **4.1 Per-clinician P&L** (these compound).

Sleeper bets from the expanded themes (marked ★ above):

- **9.2 Supervision hours tracking** — required for associate-level licensure, almost nobody handles it well, several years of data builds switching cost
- **10.1 Referral partner CRM** — durable relationships drive the highest-LTV clients; this gives owners a system instead of a contacts list
- **10.2 Review collection workflow** — Google reviews are the highest-ROI local marketing move; few practices systematize it
- **12.1 Telehealth state-licensure compliance** — once a practice gets bitten by this, they won't trust a tool without it
- **17.1 Decision journal** — meta, but pairs with AI to create a "Chief of Staff for owners" angle nobody else is offering
- **19.1 AI clinical scribe** — biggest AI play in mental health right now; wedge that pulls clinicians into the suite
- **19.3 Co-signature workflow** — required for compliance; small build that prevents real revenue loss
- **20.1 Standardized risk screening** — clinical safety + legal protection; gets owners who care about clinical quality
- **20.5 Clinician departure / transition workflow** — high-pain moment most owners only experience after losing clients to bad ones
- **21.2 Clinician video bio** — cheap, immediately marketable, every solo will want it
- **21.5 Clinician matching quiz** — doubles as a marketing asset; removes "who do I ask for?" friction
- **24.1 S-corp salary planning** — real money, real complexity, almost nobody nails it
- **25.1 Unified renewal calendar** — tiny build effort, very high gratitude per dollar
- **26.2 Owner incapacity / continuity plan** — sensitive sell, but every owner over 40 with kids worries about this
- **29.2 5-minute SLA response engine** — single highest-ROI inquiry capability; beats 90% of practices' current process out of the box
- **30.1 Natural-language query** — future of analytics UI; wedge against every dashboard-editor tool
- **31.1 Pediatric workflow** — fastest-growing specialty segment; current tools are hostile to it
- **32.1 Good Faith Estimate generation** — legally required, almost universally non-compliant; great sales opener
- **34.1 Triggered automations** — power-user feature that locks growing practices in

Three possible product narratives you could pick based on what to build first:

1. **"BI + strategy layer on top of your EHR"** — lean into 1.x, 4.x, 5.x, 16.x and the dashboard. Lowest-friction adoption. EHR bridge (18.1) is the wedge.
2. **"Growth engine for solo and small group practices"** — lean into 6.3, 10.x (esp. 10.1 / 10.2 / 10.4), and 2.x. The product becomes the practice's marketing brain. Strongest for solo practitioners.
3. **"Operating system for practice owners"** — lean into 9.x, 12.x, 17.x. The product becomes the place the owner spends their week. Highest stickiness, longest build, biggest payoff.

These aren't mutually exclusive long-term, but they define what to build *first*. Worth discussing before locking in the next wave.
