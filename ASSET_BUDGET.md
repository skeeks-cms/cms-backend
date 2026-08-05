# Backend asset budget

Status: canonical measurement contract. Package owner: `skeeks/cms-backend`.

This budget was measured on the local SkeekS test site on 2026-08-05 through
the rendered page asset inventory. Counts cover external JavaScript and CSS
requests after a complete page load; images, inline SVG and debug-toolbar data
are reported separately by the browser and are not part of these ceilings.
Routes use development, non-concatenated assets, so the budget is deliberately
strict about dependency growth rather than production transfer size.

| Surface | Representative route | JS | CSS | Transitional ceiling |
| --- | --- | ---: | ---: | --- |
| Empty backend layout | country create with `_sxb[el]=1&_sxb[noa]=1` | 47 | 16 | 47 / 16 |
| Standard Admin table | `/~sxx/cms/admin-cms-country` | 59 | 25 | 59 / 25 |
| Standard Admin form | `/~sxx/cms/admin-cms-country/create` | 50 | 19 | 50 / 19 |
| Model card + gallery | `/~sxx/shop/admin-shop-collection/view?pk=1` | 26 | 18 | 26 / 18 |
| Standard UPA table | `/~upa/upa-sites` | 59 | 23 | 59 / 23 |
| Heavy file manager | `/~sxx/cms/admin-storage-files` | 64 | 26 | 64 / 26 |
| Admin dashboard, 3 widgets | `/~sxx/admin/admin-index/dashboard?pk=1` | 51 | 26 | 51 / 26 |

The ceilings are current migration baselines, not desirable long-term totals.
A change that crosses one must name the owning component, demonstrate that the
asset is absent when the component is absent, and record the reason here. A
decrease becomes the new ceiling after Admin/UPA light, dark and responsive
checks. Do not hide dependency growth by combining unrelated files solely to
reduce the request count.

## Classification

- **Required core:** Yii/jQuery compatibility, SkeekS `Core`/`Custom`,
  `BackendCoreAsset`, theme tokens, blocker and native toast runtime.
- **Used almost everywhere:** backend shell/app/window/PJAX compatibility,
  the current single Bootstrap compatibility layer and the legacy icon adapter.
  Bootstrap and icon fonts are migration candidates, not safe deletions.
- **Component assets:** collection filters/showings/pager, jQuery ContextMenu
  and jQuery UI sortable on configurable collections; form adapters such as
  Select2/Kartik only with their widgets; `FancyboxAssets` and
  `AdminShopGalleryAsset` only on image cards.
- **Page assets:** storage upload tools and file-manager behavior on
  `admin-storage-files`; `AdminDashboardAsset` and its CSS grid only on the
  dashboard route. jQuery UI sortable is additionally permission-scoped to
  dashboard editors, while Highcharts is owned by the disk-space widget.
- **Legacy:** jGrowl/Toastr and frontend Unify are absent from every measured
  standard route. Their compatibility bundles remain explicit opt-in only.
- **Replacement candidates:** jQuery ContextMenu and jQuery UI sortable need a
  consumer-by-consumer native replacement before removal. Select2/Kartik keep
  their semantic adapters and must remain conditional on the owning field.

`BackendPanelAsset` is an optional component boundary. Structural `.sx-panel`
rules no longer live in the global theme; default model cards and dashboard
panels register the bundle explicitly. This adds one named CSS request only to
panel consumers and keeps tables, forms and empty backend layouts unchanged.
- **Lazy-load candidates:** dashboard editing, theme customization, large file
  upload modes, charts and rare editors. Their normal shell cost must stay zero.

## Verified gallery reduction

The shop collection card previously registered `UnifyThemeStickAsset` for its
gallery. Baseline: 31 JS and 25 CSS. That dependency brought 11 identified
Unify/Slick resources: Unify core/globals/components, theme custom CSS/JS,
`hs.core`, popup, Slick CSS/JS, `hs.carousel` and stick CSS.

`ShopAdminGallery` now owns the markup and accessibility contract, while
`AdminShopGalleryAsset` contributes exactly one page CSS and one page JS file
and depends only on `BackendUiAsset`. Fancybox remains a separate component
asset for full-image viewing. Result on the same populated card: 26 JS and 18
CSS, no Unify/Slick resources, no horizontal overflow and no console errors.
Pointer, thumbnail, previous/next and ArrowLeft/ArrowRight navigation were
verified with two images in light desktop and dark mobile modes.

## Regression protocol

For each budget route, record JS/CSS counts and search loaded asset URLs for
`jgrowl`, `toastr`, `unify-core`, `unify-components` and `unify-globals`.
For conditional components, also prove the inverse page does not load them.
Check Admin and UPA in explicit light/dark themes, reset theme and viewport,
and keep the browser console free of errors. Never remove an asset merely
because it appears in the network inventory; resolve its PHP bundle and actual
DOM/runtime consumer first.
