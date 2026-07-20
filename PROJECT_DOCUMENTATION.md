# Nispaksha Awaj — WordPress Custom Child Theme

Comprehensive developer and AI agent documentation for the **Nispaksha Awaj** (`https://www.nispakshawaj.com`) WordPress child theme.

---

## 📌 Project Overview

- **Live Website**: [https://www.nispakshawaj.com](https://www.nispakshawaj.com)
- **GitHub Repository**: `https://github.com/pchand89/nispakshawaj.git`
- **Deployment Path on Server**: `public_html/wp-content/themes/child-theme`
- **Parent Theme**: `maglist` (Version 1.4 by Eagle Vision IT)
- **Design Inspiration**: [Ratopati.com](https://www.ratopati.com) & [OnlineKhabar.com](https://www.onlinekhabar.com)

This project contains a custom WordPress child theme designed to transform **Nispaksha Awaj** into a modern, high-impact Nepali news portal modeled directly after **Ratopati.com**, featuring distinct multi-colored section blocks, centered logo header, lead headline stacks, and Ratopati Red (`#bf1e2e`) branding.

---

## 🏗️ Repository & Theme File Structure

All theme files are placed directly in the repository root for seamless git deployment into `public_html/wp-content/themes/child-theme`:

```
nispakshawaj/
├── style.css                   # Main theme design system (Ratopati Red #bf1e2e, multi-colored section blocks)
├── functions.php               # Theme enqueues, fonts, helper functions, SEO tags, widget & menu registrations
├── front-page.php              # Custom homepage template rendering lead stack, ticker, category blocks & sidebar
├── single.php                  # Single post template (breadcrumb, meta, related posts, share, comments)
├── archive.php                 # Category/tag/date/author archive template (card grid + pagination)
├── search.php                  # Search results template (card grid + pagination + empty state)
├── 404.php                     # Not-found page with search box and trending widget
├── header.php                  # Centered logo header, top utility bar, search overlay & primary navigation
├── footer.php                  # 4-column slate top footer (#0f172a) + signature red bottom footer bar (#bf1e2e)
├── screenshot.png              # Theme preview thumbnail for WordPress Admin
├── README.md                   # Repository introduction & quick start guide
├── PROJECT_DOCUMENTATION.md    # Full context documentation for AI agents (Cursor, Claude, Copilot)
├── js/
│   └── custom.js               # Dark mode persistence, sticky nav, back-to-top, mobile drawer, search overlay
└── template-parts/
    ├── hero-section.php        # Lead Headline Stack (3 top lead stories with centered titles & images)
    ├── breaking-news.php       # Ratopati Red ticker bar with black 'ब्रेकिङ' badge
    ├── category-section.php    # Multi-themed category block renderer (White, Navy, Cream, Purple, Green, Black)
    ├── news-card.php           # Universal news card component (Standard, Portrait, Overlay variants)
    └── sidebar-trending.php    # Numbered trending news widget (1-8) + registered "Homepage Sidebar" widget area
```

> Because `single.php`, `archive.php`, `search.php` and `404.php` all call `get_header()` / `get_footer()`,
> they automatically inherit this child theme's Ratopati-branded header/footer/nav — so every page on the
> site now shares the same design system as the homepage, not just `/`.

---

## 🎨 Color Palette & Section Themes

| Component / Section | Background Color | Hex Code | Text Color |
|---------------------|------------------|----------|------------|
| **Brand Primary Red** | Ratopati Red | `#bf1e2e` | `#ffffff` |
| **Topbar & Page Alt** | Light Slate | `#f8fafc` | `#4a5568` |
| **Top Footer** | Dark Slate | `#0f172a` | `#cbd5e1` |
| **Bottom Footer Bar** | Ratopati Red | `#bf1e2e` | `#ffffff` |
| **`प्रदेश` (Province)** | Dark Navy Blue | `#0d2238` | `#ffffff` |
| **`विचार / ब्लग` (Opinion)** | Warm Light Cream | `#fef8e7` | `#000000` |
| **`मनोरञ्जन` (Entertainment)** | Dark Purple | `#350a2e` | `#ffffff` |
| **`खुलामञ्च` (Khulamanch)** | Dark Forest Green | `#163829` | `#ffffff` |
| **`खेलकुद` (Sports)** | Deep Space Blue | `#0a1128` | `#ffffff` |
| **`रातोपाटी टीभी` (TV/Video)** | Pure Black | `#000000` | `#ffffff` |

---

## ⚡ Key Developer & AI Agent Gotchas

> [!IMPORTANT]
> **1. Category Slugs in Live WordPress Database**:  
> Category slugs in the live WordPress database on `nispakshawaj.com` are stored in **raw Devanagari/Nepali**, NOT English transliterations.  
> - **Correct Slugs**: `समाचार`, `राजनिती`, `व्यवसाय`, `कृषि`, `अपराध`, `स्वास्थ्य-विज्ञान-र-प्रव`, `शिक्षा-साहित्य`, `खेलकुद`, `मनोरञ्जन`, `समाज`, `स्थानीय-तह-विकास`, `विदेश-कूटनीति`, `विविध`.  
> - Do NOT use English slugs like `'samachar'` or `'rajniti'`, as `get_category_by_slug()` will fail and return `false`.  
> - `functions.php` includes a 4-stage fallback in `nispaksha_get_category_posts()` (raw slug ➔ urlencoded slug ➔ sanitized title ➔ category name) to ensure queries never fail.

> [!IMPORTANT]
> **2. Thumbnail Fallback Logic**:  
> `nispaksha_get_thumb_url( $post_id, $size )` handles posts without an explicit featured image by extracting the first `<img>` tag from `post->post_content`. If no image exists, it falls back to `LogoNewTextBorder-1.png`. Never remove this fallback, or posts without featured images will render broken image icons in `<img src="" />`.

> [!IMPORTANT]
> **3. Server Deployment Target**:  
> The git repository is mounted/pulled directly into `public_html/wp-content/themes/child-theme`. Therefore, all theme files (`style.css`, `functions.php`, `front-page.php`, `header.php`, `footer.php`) must remain at the **root level of the repository**.

> [!IMPORTANT]
> **4. Homepage De-duplication via `$GLOBALS['nispaksha_shown_ids']`**:  
> `front-page.php` renders ~13 category blocks, and two of them intentionally reuse the same category slug (`शिक्षा-साहित्य` for both the "opinion" and "portrait/साहित्य" blocks). To avoid showing the exact same articles twice, every `template-parts/category-section.php` call appends the IDs of the posts it just displayed to `$GLOBALS['nispaksha_shown_ids']`, and each subsequent `get_template_part()` call on the homepage passes that running list back in as `'exclude'`. If you add new homepage sections, pass `'exclude' => $GLOBALS['nispaksha_shown_ids']` too, or the same story can resurface.

> [!IMPORTANT]
> **5. `अपराध` (Crime) category powers the "टीभी / भिडियो" (TV/Video) block**:  
> There's no dedicated video/TV category yet, so `front-page.php` block #10 borrows the `अपराध` (crime) category slug purely to have *some* content in that themed black section. This is a placeholder, not a real mapping — create an actual video category (or the planned Customizer category picker below) and repoint that block before launch, otherwise crime stories will display under a "TV/Video" heading.

---

## ✅ Completed Work So Far

1. **Repository Setup**: Initialized Git repository, connected to GitHub (`pchand89/nispakshawaj`), created initial commit structure.
2. **Child Theme Base**: Created valid `style.css` declaring `Template: maglist` and enqueued parent + child styles in `functions.php`.
3. **Typography**: Imported Devanagari Google Font **`Mukta`** (`400`, `500`, `600`, `700`, `800`, `900` weights) and English font **`Poppins`**.
4. **Ratopati Header**: Built topbar with date in Nepali, centered logo header (`LogoNewTextBorder-1.png`), search overlay button, dark mode toggle button, and primary navigation bar with red accent border.
5. **Ratopati Lead Headline Stack**: Built 3-item centered lead story banner (`.rp-lead-item`) with extra-large bold titles (`42px`, 900 weight), category badge, excerpt, and centered featured image.
6. **Ratopati Red Ticker Bar**: Created animated horizontal scrolling ticker with black `ब्रेकिङ` badge.
7. **Multi-Colored Section Blocks**: Implemented distinct section themes (`province` navy, `opinion` cream, `entertainment` purple, `green` khulamanch, `sports` deep blue, `tv` black) matching Ratopati screenshots.
8. **Ratopati Sidebar & Footer**: Built numbered `ट्रेन्डिङ` widget (1-8 with red circular badges) and 4-column slate top footer + signature red bottom footer bar (`#bf1e2e`).
9. **Bug Fixes**:
   - Resolved category slug mismatch by mapping exact Nepali slugs.
   - Fixed broken thumbnail image icons via content image extraction fallback.
   - Fixed mobile menu drawer CSS resets to prevent unstyled lists on desktop.
10. **Site-wide templates** (`single.php`, `archive.php`, `search.php`, `404.php`): every page type now shares the Ratopati header/footer/nav and card design system instead of falling back to the parent `maglist` theme's own markup.
11. **Working mobile navigation & search**: the hamburger button now actually opens a slide-in drawer (with backdrop, `Esc`-to-close, and body scroll lock), and the search icon opens a real search overlay — both were previously non-functional (no JS was wired up).
12. **Functional dark mode**: introduced semantic CSS variables (`--bg-card`, `--bg-surface`, `--bg-surface-alt`, `--border-color`) so toggling dark mode now actually restyles the topbar, header, nav, cards, and widgets, instead of only changing 3 variables that nothing used.
13. **Fixed undefined `--bg-card` CSS variable** that made `.rp-card` background fall back to `transparent`.
14. **Registered widget areas are now live**: `dynamic_sidebar()` calls were added so admin-added widgets in *Appearance → Widgets → Homepage Sidebar / Footer Column 1* actually render (previously the sidebars were registered but never output anywhere).
15. **Social icons wired up**: Facebook/Twitter(X)/YouTube URLs set in the Customizer now render as icon links in the footer (previously the Customizer settings existed but nothing read them).
16. **Homepage post de-duplication**: a shared exclude list now flows through every homepage section so the same story never appears twice (see Gotcha #4 above).
17. **SEO**: added Open Graph, Twitter Card meta tags, and `NewsArticle`/`BreadcrumbList` JSON-LD schema (auto-disabled if Yoast/Rank Math/AIOSEO is active) plus a Nepali breadcrumb trail on singular/archive/search pages.
18. **Reliability fixes**: replaced the deprecated `current_time( 'timestamp' )` call with `current_time( 'U' )`, and made the default thumbnail fallback check for a local theme asset first before depending on a hardcoded production media URL.

---

## 🔮 Next Steps & Future Roadmap

- [ ] **Fix the `अपराध` → "टीभी / भिडियो" placeholder mapping** (see Gotcha #5) — either create a real video/TV category or repoint that homepage block.
- [ ] **Customizer Settings**: Add controls in WP Customizer to allow admin users to pick which categories display in which color blocks.
- [ ] **Ad Placements**: Add widget areas for leaderboard ad banners (`home-above-header`, `home-between-sections`, `sidebar-ad`).
- [ ] **Video Player Integration**: Enhance `रातोपाटी टीभी` (TV / Video block) to embed YouTube/Vimeo video modals directly on click.
- [ ] **Utility Tools**: Add Preeti-to-Unicode & Date Converter popups in the topbar utility links.
- [ ] **Performance Optimization**: Add WebP image conversion and CSS minification.
- [ ] **Add `/img/default-thumb.png`**: `nispaksha_default_thumb()` will automatically switch to a local fallback image once this file is added to the theme (currently falls back to a production upload URL).
- [ ] **Comment styling**: `single.php` now calls `comments_template()`, but the comment form/list still relies on parent-theme (`maglist`) styling — consider a themed `comments.php` for full visual consistency.
