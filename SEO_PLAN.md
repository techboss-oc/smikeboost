# SmikeBoost SEO Playbook (Nigeria Focus)

| Page                                           | Primary Intent                   | Nigerian Keyword Cluster                                                                                     | Supporting Actions                                                                                                                |
| ---------------------------------------------- | -------------------------------- | ------------------------------------------------------------------------------------------------------------ | --------------------------------------------------------------------------------------------------------------------------------- |
| Home (`/`)                                     | Brand, broad SMM panel awareness | `smm panel nigeria`, `buy instagram followers lagos`, `cheap smm services naira`                             | Update hero copy to mention "Nigeria" explicitly, highlight Naira payments, add FAQ targeting local banking + delivery speed.     |
| Services (`/services`)                         | Service catalogue, pricing       | `social media marketing services nigeria`, `instagram likes nigeria price`, `nigeria tiktok followers panel` | Add structured data for ServiceCategory, include pricing snippets in NGN, create internal links to each platform subsection.      |
| About (`/about`)                               | Trust, brand story               | `smm panel company in nigeria`, `social media growth experts lagos`                                          | Add timeline with Nigerian client highlights, embed local awards/testimonials, link to Contact/Help CTAs.                         |
| Contact (`/contact`)                           | Lead capture                     | `smm panel support nigeria`, `whatsapp smm support lagos`                                                    | Include WhatsApp number in schema + clickable link, add Google Map of Lagos office, mention 24/7 Nigerian support hours.          |
| How It Works (`/how-it-works`)                 | Education                        | `how smm panel works nigeria`, `reseller smm nigeria tutorial`                                               | Add step-by-step anchored sections, embed short explainer video with Nigerian case study.                                         |
| Blog (`/blog`)                                 | Content hub                      | `instagram marketing nigeria tips`, `tiktok trends nigeria 2025`                                             | Publish 4+ long-form posts/month targeting Nigerian search trends, add author bios, enable category pages.                        |
| FAQ (`/faq`)                                   | Objection handling               | `smm panel nigeria payment methods`, `is buying followers legal in nigeria`                                  | Add schema FAQ markup per question, answer compliance/tax questions.                                                              |
| Help Center (`/help-center`)                   | Support knowledge base           | `smikeboost help nigeria`, `flutterwave smm deposit issue`                                                   | Convert to topical articles, add search component, link to tickets.                                                               |
| Status (`/status`)                             | Service health                   | `smm panel service status nigeria`                                                                           | Include uptime % and incident history for trust/AI search references.                                                             |
| API Docs (`/api-docs`)                         | Developer onboarding             | `smm panel api nigeria`, `reseller api ngn`                                                                  | Add copy referencing NGN pricing, include downloadable Postman collection, implement schema `SoftwareApplication`.                |
| Privacy (`/privacy-policy`) & Terms (`/terms`) | Compliance                       | `smm panel privacy nigeria`, `anti scam policy nigeria`                                                      | Mention NDPR (Nigeria Data Protection Regulation) compliance, include DPA contact.                                                |
| Dashboard pages                                | Conversion, retention            | `reseller smm dashboard nigeria`, `buy followers nigeria instant delivery`                                   | Add unique titles/meta within layout, include breadcrumb schema, ensure app copy references NGN and Nigerian telecom reliability. |

## Content Expansion Roadmap

1. **Publish 12 flagship landing pages** for high-volume Nigerian cities/industries (e.g., "TikTok Growth for Lagos Fashion Brands").
2. **Cluster blog content** around social proof queries ("best smm panel in nigeria", "is buying followers safe"), targeting Featured Snippets and AI summaries.
3. **Build data-backed case studies** (music artistes, fintech startups) including quantifiable metrics and embed them within service pages for EEAT signals.
4. **Launch Nigerian social proof signals**: embed Naira payment screenshots, CBN compliance notes, and testimonials referencing local influencers.

## Technical SEO Enhancements

- Implement XML sitemap + auto ping to Google/Bing; include dashboard routes that return 200.
- Add RSS feed for blog to assist AI content aggregators.
- Configure hreflang for `en-NG` / `en-US` if global expansion planned.
- Enable server-side compression + caching headers for assets.
- Set up schema for FAQ, Breadcrumbs, Product/Service details using NGN pricing.
- Monitor Core Web Vitals via Search Console; optimize hero images under 80KB WebP.

## Authority & Distribution

- Register SmikeBoost on Google Business Profile (service-area Lagos/Abuja) and add consistent NAP details in footer.
- Secure partnerships/mentions from Nigerian tech blogs, digital marketing communities, and universities (backlinks).
- Syndicate evergreen blog posts on Medium/LinkedIn with canonical pointing back to site.
- Launch YouTube & TikTok educational series; embed videos on blog posts for dwell time.

## Measurement & AI Search Readiness

- Configure GA4 + Search Console with Nigeria geo filters.
- Track keyword positions using tools filtered to google.com.ng.
- Mark up critical answers (pricing, delivery time) with structured data so AI assistants can cite the site.
- Maintain a changelog/status page referencing incident transparency for AI trust signals.

## Implementation Sprint: Sitemap + FAQ + NDPR

1. **XML Sitemap**
   - Expose `/sitemap.xml` via router that lists all public pages + blog posts (auto generated array fallback for now) with `<lastmod>` timestamps.
   - Include dashboard entry points but flag them with lower priority (`0.3`).
   - Add ping hooks (simple curl) in deployment instructions for Google and Bing after updates.
2. **FAQ Schema**
   - Convert existing FAQ section on `home` and `faq` pages into JSON-LD (`FAQPage`) generated from a structured PHP array so content + schema stay in sync.
   - Ensure each question references Nigerian payment/legal context for topical relevance.
3. **NDPR Privacy Update**

   - Expand `privacy-policy.php` with a dedicated NDPR section covering lawful basis, data residency, and Data Protection Officer contact.
   - Add opt-out instructions + response timelines (7 days) to satisfy Nigeria Data Protection Regulation expectations.
   - Reference Flutterwave/Paystack as sub-processors with links to their compliance statements.

4. **On-Page Copy & StoryBrand Refresh**
   - Follow `ONPAGE_SEO_COPY.md` for hero messaging, meta tags, CTAs, and section headings across every public page.
   - Each page must articulate: Character (Nigerian creator/agency) → Problem → SmikeBoost as Guide → 3-step Plan → Success + Failure avoidance.
   - Update PHP view files with suggested copy, include localized case studies, and ensure CTAs point to `register` + `contact`.
   - Add relevant JSON-LD (`Service`, `HowTo`, `FAQPage`, `SoftwareApplication`) as outlined per page.
