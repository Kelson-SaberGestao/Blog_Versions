# Quality Blog — English Prototype

Static HTML prototype for the English-language version of *Blog da Qualidade* (ForLogic / Qualiex), rebuilt with a lighter, photo-driven design system. No backend, no build step — each page is a single self-contained HTML file.

## Files

| File | What it is |
|---|---|
| `index.html` | Homepage — hero, latest articles, "Browse by topic" category sections, free assets, Quality Assistant promo |
| `post.html` | Single post template — linked from the homepage's featured post |

## What's actually working

- **Responsive layout** — desktop, tablet and mobile breakpoints, including a dedicated mobile header: hamburger button opening a slide-in menu with accordion sub-menus (tap to expand "Software for Quality", "Categories", "Free Assets")
- **Homepage → post navigation** — the featured post card on the homepage links through to `post.html`; the post page's logo and breadcrumb "Home" link back to the homepage
- **Category color-coding** — each "Browse by topic" section has its own accent color, reused in its "Browse all" link and photo tint
- **Real photo covers** — every article card uses a real stock photo (Lorem Picsum), tinted by category/brand color
- **Client-side interactions on the post page** (no backend — everything resets on reload):
  - Newsletter signup form → shows a success message
  - "Download free" checklist button → shows a sent confirmation
  - Comment form → posted comments are appended to the list live, with an updating count
  - Copy-link share button → copies the page URL via the clipboard API and shows "Link copied!"
  - "Back to top" button → appears after scrolling, scrolls smoothly back up
- **Light mode only** — dark mode was intentionally removed for now

## What's still a placeholder

- Every other link (nav dropdown items, category/tag links, footer links, social icons, "Browse all" on the homepage, search bar) points to `#` or isn't wired to a real destination yet
- Search is visual only — no real search functionality
- Newsletter and comment submissions are simulated in the browser only; nothing is sent to a server or stored
- Photos are generic free stock images standing in for real brand photography

## Viewing it

- **Locally**: open `index.html` or `post.html` directly in a browser, or serve the folder (`python3 -m http.server`) for the most accurate preview
- **Live**: once GitHub Pages is enabled for this repo (Settings → Pages → Deploy from branch → `main` → `/root`), it will be available at `https://kelson-sabergestao.github.io/Blog_Versions/`
