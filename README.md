SkeekS CMS backend
===================================

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist skeeks/cms-backend "*"
```

or add

```
"skeeks/cms-backend": "*"
```

Configuration app
----------


```php
[
    'bootstrap'     => ['backendAdmin'],
    'components'    =>
    [
        'backendAdmin' =>
        [
            'id'                => 'id',
            'class'             => 'skeeks\cms\backend\BackendComponent',
            'controllerPrefix'  => 'admin',
            'urlRule'           => [
                'urlPrefix' => '~admin'
            ],
            'allowedIPs' => [
                '91.*',
                '92.219.167.252',
            ]
        ],
    ],
]
```

The canonical implementation runbook for new Admin, UPA and cabinet sections
is [`BACKEND_UI_GUIDE.md`](BACKEND_UI_GUIDE.md). It covers collection modes,
entity links and drawers, surface selection, conditional assets, themes,
verification and the vendor/project boundary.

Semantic palette
----------------

`BackendTheme::$palette` is the opt-in storage contract for project and future
user-editable themes. It accepts partial `light` and `dark` maps with only ten
base colors: `accent`, `canvas`, `surface`, `surfaceMuted`, `text`, `textMuted`,
`border`, `success`, `warning`, and `danger`.

```php
'view' => [
    'theme' => [
        'class' => \skeeks\cms\backend\themes\BackendTheme::class,
        'palette' => [
            'light' => ['accent' => '#1769aa'],
            'dark' => ['accent' => '#73b7f2'],
        ],
    ],
],
```

The backend validates hex colors and expands them into the full public
`--sx-color-*` facade, including state, soft, `on-soft`, `on-surface`, contrast,
and focus colors. Status fills consume the generated `*-on-soft` colors and
standalone semantic text consumes `*-on-surface`, so custom palettes preserve
the selected base color while maintaining at least WCAG AA contrast.
Component tokens remain internal. Brand gradients and product-specific layout
geometry stay in project CSS; arbitrary CSS variables are not accepted here.

The shell customizer is a lazy, scoped editor: its markup, CSS and JavaScript
load only when requested, and `admin`/`upa` keep separate user/site/default
settings. The browser preview must expand the same dependent facade as PHP,
including accessible status foregrounds; closing without saving removes the
preview. Only the ten palette keys above and the per-theme header appearance
are editable—compatibility and component tokens are never form fields.

Backend notifications
---------------------

`sx.notify` is the single notification API for Admin, UPA and future cabinets.
The standard `BackendCoreAsset` graph loads `BackendNotifyAsset` and the native,
dependency-free `ComponentNotifyToast`; it does not load jGrowl or Toastr.
Compatible entry points are `defaul`, `notice`, `info`, `success`, `warning`,
`error`, `fail`, plus `show(text, {type: ...})` for a dynamic type and `clear()`
for cleanup.

Toast options include `duration` (`life` remains an alias), `position`, `title`
(`header` remains an alias), `sticky`, `closable`, `closeLabel`,
`pauseOnHover`, `showProgress`, `maxVisible`, `actions`, `id`/`key`, `image`,
`onClick` and `onClose`. The compatibility default accepts HTML; new code that
passes server responses, validation text or any user-derived value must set
`allowHtml: false`. Colors, focus, stack offsets, progress and reduced-motion
behavior belong to the shared backend notification tokens. The legacy
`ComponentNotifyJgrowl` remains explicit opt-in only.

Backend collection content
--------------------------

The measured dependency ceilings and classification of core, component,
page-specific, legacy and lazy assets are maintained in
[`ASSET_BUDGET.md`](ASSET_BUDGET.md). New shared UI must stay inside those
ceilings or document the owning conditional component and measured delta.

Panel structure is opt-in through `BackendPanelAsset`; emitting `.sx-panel`
without registering that bundle is not supported. Dashboard layout belongs to
`AdminDashboardAsset` in `skeeks/cms-backend-admin`, and chart/upload/table
dependencies remain owned by the individual dashboard widget that needs them.

Status: **canonical**. Package owner: `skeeks/cms-backend`.

Standard section pages use `BackendGridModelAction` as their composition
contract and `BackendSectionHeader` as the single header renderer. Configure
`presentationMode => BackendGridModelAction::PRESENTATION_PAGE` for a standalone
section, then describe its plain-text `pageHeader` (`title`, `description`,
semantic `icon`, `actions`). Controller actions referenced through
`backendAction` preserve their access rules and drawer/navigation behavior; the
first resolved action is primary and later actions are secondary unless an
explicit `variant` is supplied. `actions => false` is valid for read-only UPA
sections.

The remainder of the standard page is already owned by the collection stack:
backend showings are view presets, `DynamicFiltersWidget` owns search and
additional filters, `GridViewWidget`/`ListViewWidget` own table or collection
presentation, pagination, summary, per-page and service/multi actions, and
`EmptyStateWidget` distinguishes an empty collection from filtered no-results.
New sections configure these components and do not copy page-header markup or
add local page CSS. Admin countries and the UPA sites/bills pages are the
representative adopters.

`GridViewWidget` and `ListViewWidget` provide the common collection geometry.
Domain packages describe cell content with the semantic classes below instead
of inline typography, colors or Bootstrap layout:

| Role | Contract |
| --- | --- |
| Cell composition | `sx-collection-cell` |
| Stacked content | `sx-collection-cell--stack` |
| Primary entity or value | `sx-collection-cell__primary` |
| Supporting metadata | `sx-collection-cell__secondary` |
| Low-priority metadata | `sx-collection-cell__subtle` |
| Money | `sx-collection-cell__amount` |
| Counts and technical metrics | `sx-collection-cell--metric` |
| Compact state | `sx-status` with a semantic modifier |
| Entity preview | `sx-preview-card` and its `__media`, `__content`, `__title`, `__meta` parts |
| Compact preview column | `sx-preview-card-column` |

Use `sx-text--success`, `sx-text--warning` or `sx-text--danger` only when the
value itself represents that state. A normal entity title, amount or count is
neutral. Do not communicate state with a hard-coded whole-row background,
opacity, inline color or `<b>` tag.

```php
return Html::tag(
    'span',
    Yii::$app->formatter->asInteger($count).' шт.',
    ['class' => 'sx-collection-cell sx-collection-cell--metric']
);
```

When an entity has a standard backend card, use `BackendEntityLink` for its
title. It preserves a normal `view` URL and delegates drawer behavior to the
shared controller-actions runtime. Pass controller-specific routing values in
`urlParams`; they are applied to both the normal fallback URL and the enhanced
drawer request. Do not add page-local drawer JavaScript.

In an interactive tree, keep the primary node label as that normal entity
link. Create, sort, expand and other tree operations are explicit buttons or
real navigation links with their existing behavior hooks; they must not be
`href="#"` proxies. Nested entity links remain independent of the node label.

The contract consumes `BackendThemeAsset` tokens, supports explicit light and
dark themes, and wraps or truncates through shared collection rules. It adds no
AssetBundle beyond the normal backend collection assets. Domain-specific
columns may compose these roles but must not redefine their global geometry or
palette.

Domain detail cards may keep their own information layout, but their surfaces,
borders, text hierarchy, focus feedback and status fills must resolve through
the semantic `--sx-color-*`, `--sx-shadow-*` and `--sx-status-*` tokens. A
detail card must not ship a second fixed light palette beside the backend
theme.

Compatibility and migration
---------------------------

- **canonical:** `sx-collection-cell*`, `sx-preview-card*`, `sx-status*` and
  `BackendEntityLink`/`BackendEntityLinkColumn` for new backend collection code;
- **compatibility:** `sx-collection-item*`, list and backend-grid row names are
  temporary one-way aliases to the canonical `--sx-collection-row-*` tokens;
- **deprecated for new code:** inline colors and sizes, Bootstrap row/column
  markup inside identity cells, and module-local drawer triggers;
- **migration condition:** remove a compatibility alias only after searches of
  PHP, CSS and JavaScript show no remaining consumer and representative Admin
  and UPA collections pass light, dark and responsive checks.

The verified migration set currently contains:

- the UPA bill list as the visual collection baseline;
- Admin companies for semantic metric values;
- Admin deals for primary and related entity links;
- Admin payments for the primary payment link, clients, optional direct
  company/order/check columns and related entities;
- Admin bills for the primary bill link and related documents, clients,
  contractors and deals;
- Admin bill, document and contractor detail cards for their related entities,
  semantic surfaces and status colors.
- Admin telephony calls for client entity links, semantic call metadata and
  statuses, plus a page-specific responsive audio/layout asset.
- The shared `UserColumnData` identity cell and Admin bonus transactions for
  semantic user previews, signed amounts, comments and related orders.
- Admin shop checks for primary and related entity links, neutral amounts,
  document metadata and compact semantic fiscalization statuses.
- Shared user and worker preview widgets for normal avatar/title entity links,
  semantic preview geometry and uncached live online/work state.
- Active Hosting server and VPS-tariff collections for normal entity links,
  stacked provider metadata, compact infrastructure metrics and semantic money.
- The shared `BackendEntityLinkColumn`, verified on populated payment-system
  rows and adopted by payment-system, delivery and marketplace primary titles.
- Shop VAT, store and supplier collections for normal primary entity links;
  the populated store/supplier preview cards also use shared image, stacked
  metadata and drawer geometry without inline sizing or `href="#"` triggers.
- Discount, cashbox, cloud-cashbox, sticker, store-document and store-property
  collection configs now use explicit controller routes; empty-state routes
  were checked alongside the populated VAT/store/supplier regressions.
- Populated shop order-status and price-type collections use normal fallback
  URLs, shared drawers, stacked metadata and semantic status/marker roles.
- Order, brand, collection, store-product and product-import primary cells now
  compose `BackendEntityLink` with shared preview-card media/title geometry;
  their empty-state routes were checked without document overflow or legacy
  `href="#"` triggers.
- Document, task, project, web-notification and activity-log references, plus
  payment, cashbox, cashbox-shift, order and store-document detail references,
  use semantic entity links. Direct controller-action widgets remain only on
  explicit edit/action controls in the inspected templates.
- The company detail page uses the shared responsive detail layout and semantic
  contact/contractor links without a second fixed light palette. The department
  tree uses semantic surfaces, normal department/user links and real action
  buttons; both were checked in light, dark and mobile modes.
- Client and worker profile pages use the same responsive detail layout, real
  `tel:`/`mailto:` contact links, explicit telephony/edit controls and semantic
  related company, department, user and contractor links. Both profiles were
  checked in light, dark and mobile modes without horizontal overflow.
- The legacy `DefaultActionColumn` is a compatibility facade over
  `BackendEntityLinkColumn`: it resolves the owning grid controller and model
  primary key automatically, while still accepting explicit overrides.
  Rich legacy cells can pass deliberately trusted composed markup through the
  inherited `content` callback; extra route context belongs in `urlParams`.
  Populated measure, currency and CMS reference routes were checked with normal
  `view` fallbacks in light, dark and mobile modes without horizontal overflow.
- Site phone, email, social-network and address collections use explicit
  `BackendEntityLinkColumn` routes with semantic stacked labels. Their populated
  and empty related lists were checked in light desktop and dark mobile modes
  without legacy `href="#"`, JavaScript errors or horizontal overflow.
- Site, language and country identity cells use shared preview-card geometry
  with separate normal entity links for media and title. Populated lists and a
  site model drawer were checked in light desktop and dark mobile modes without
  legacy triggers, confirmation dialogs or horizontal overflow.
- Content, section-type and user-property dictionaries use explicit entity-link
  columns while keeping handler names, codes and SkeekS IDs as semantic
  supporting metadata. Seven populated/empty routes and a content model drawer
  were checked in light desktop and dark mobile modes without legacy triggers
  or horizontal overflow.
- Saved-filter and own-contractor identity cells use shared preview-card
  geometry with separate safe entity links for media and title. Their lists and
  the custom department tree were checked in light desktop and dark mobile
  modes without legacy triggers or horizontal overflow; the department
  controller's obsolete commented grid configuration was removed.
- Company, contractor, user and content-element identity cells now use explicit
  safe entity links for their title and preview media. Storage-file titles use
  the same model-card fallback while the preview image remains a direct file
  link. No application controller retains an `sx-trigger-action` hook; the
  shared grid handler remains temporarily as a compatibility layer for external
  consumers.
- The surface hierarchy is explicit: `sx-surface` owns low-level appearance,
  `sx-block` remains the simple legacy-compatible content block, and `sx-panel`
  owns structured header, body, actions and footer composition. Compact and
  responsive variants use the same theme tokens. The legacy Admin dashboard
  panel widget now emits this canonical structure while retaining its public
  classes and optional streamed-body API.
- `BackendModelHeader` is the canonical model-page and drawer header renderer.
  It owns the safe back link, optional media, encoded title, conventional
  ID/date/author metadata, status/toolbar/action slots and the standard
  confirmed delete control. The generic fallback, bill and contractor headers
  use it without copying the outer markup; domain slots remain explicitly
  trusted PHP markup owned by their package. Repeated domain-specific header
  behavior belongs in a thin adapter that extends `BackendModelHeader` instead
  of another view template: CMS user and worker pages now share
  `CmsContactModelHeader` for avatar, online state, contact metadata, quick
  access and account actions. Company pages keep the generic header because a
  company is not a contact account.
- `BackendQuickAccessFavoriteButton` is the declarative trigger for the shared
  quick-access runtime. It owns the serializable item payload, accessible
  pressed state and lightweight inline star icon; model views must not copy
  favorite button markup or add a page-specific asset bundle.
- Task and project headers now compose relationship metadata and quick access
  through the canonical header slots. Shop brand and collection pages share a
  thin `ShopCatalogModelHeader` adapter for supplier state, public links and
  standard model actions instead of maintaining duplicate outer templates.
- Public content-element and site-tree cards share `CmsPublicModelHeader` for
  indexing, adult, supplier, canonical, redirect and public-link state.
  Document cards compose their issued date and type through canonical metadata
  slots and only expose the standard delete control while the document remains
  editable. Unwired legacy deal and department partials are safe generic
  compatibility wrappers and are deliberately not activated by this migration.
- The shop content-element detail view still registers
  `UnifyThemeStickAsset`, which transitively loads the frontend Unify core,
  components, globals and custom assets. This is a measured page-level asset
  debt for the asset-budget stage; none of the model-header widgets introduces
  that dependency.

These entity links keep their existing presentation classes while replacing
`href="#"` or JS-only triggers with a normal backend `view` URL. The shared
drawer now requests the explicit safe `view` action, backed by the standard
read-only model card when a controller does not provide a richer view. It never
infers a destructive default from action ordering. Direct navigation is the
keyboard, new-tab and no-JavaScript fallback.

Known compatibility consumers still include the legacy Hosting VPS/site
controllers that are not present in the current Admin navigation. They must be
inspected individually because some
`AjaxControllerActionsWidget` instances are action buttons or embedded editors
rather than entity links.

Representative regression routes are `/~sxx/cms/admin-cms-company`,
`/~sxx/cms/admin-cms-department`,
`/~sxx/cms/admin-user`, `/~sxx/cms/admin-worker`,
`/~sxx/cms-measure/admin-measure`, `/~sxx/money/admin-currency`,
`/~sxx/cms/admin-cms-deal`, `/~sxx/cms/admin-cms-bill`,
`/~sxx/cms/admin-cms-document`, `/~sxx/cms/admin-cms-contractor`,
`/~sxx/shop/admin-payment`, `/~sxx/shop/admin-bonus-transaction`,
`/~sxx/shop/admin-shop-check`, `/~sxx/shop/admin-pay-system`,
`/~sxx/shop/admin-marketplace`, `/~sxx/cms/admin-cms-telephony-call`,
`/~sxx/hosting/admin-servers`, `/~sxx/hosting/admin-vps-tariff` and
`/~upa/upa-bill`. The remaining domain grids
must be audited before they are classified as migrated.

Links
-----
* [Web site](https://cms.skeeks.com)
* [Author](https://skeeks.com)
* [ChangeLog](https://github.com/skeeks-cms/cms-backend/blob/master/CHANGELOG.md)

___

> [![skeeks!](https://skeeks.com/img/logo/logo-no-title-80px.png)](https://skeeks.com)  
<i>SkeekS CMS (Yii2) — fast, simple, effective!</i>  
[skeeks.com](https://skeeks.com) | [cms.skeeks.com](https://cms.skeeks.com)


