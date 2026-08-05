# SkeekS Backend UI implementation guide

Status: **canonical**. Owner: `skeeks/cms-backend`. Admin assembly owner:
`skeeks/cms-backend-admin`.

Use this guide for new Admin, UPA and future cabinet sections. Domain queries
and domain markup stay in their `skeeks/*` package; reusable structure, tokens
and interaction contracts belong here.

## 1. Start a standard section

Extend `BackendModelStandartController` and configure its existing `index`
action. Do not create an index view merely to add a heading or toolbar.

```php
use skeeks\cms\backend\actions\BackendGridModelAction;

public function actions()
{
    return ArrayHelper::merge(parent::actions(), [
        'index' => [
            'presentationMode' => BackendGridModelAction::PRESENTATION_PAGE,
            'pageHeader' => [
                'title' => \Yii::t('app', 'Products'),
                'description' => \Yii::t('app', 'Catalog products and their state.'),
                'icon' => 'folder',
                'actions' => [
                    ['backendAction' => 'create', 'icon' => 'plus'],
                ],
            ],
            'filters' => [
                'visibleFilters' => ['name', 'active'],
            ],
            'grid' => [
                'visibleColumns' => ['checkbox', 'actions', 'name', 'active'],
            ],
        ],
    ]);
}
```

`BackendSectionHeader` is normally composed by `BackendGridModelAction`.
Referencing `backendAction` preserves visibility, access rules and drawer
behavior. The first resolved action is primary; later actions are secondary.
Use `actions => false` for a read-only UPA section.

## 2. Choose the collection presentation

| Need | Contract |
| --- | --- |
| Dense operational data, sorting and many columns | Default `GridViewWidget` table |
| Repeated content items with custom internal layout | `ListViewWidget` and an `itemView` |
| Cards or tiles | `ListViewWidget`; make the item view compose `sx-surface`, not a third data pipeline |

```php
'grid' => [
    'class' => \skeeks\cms\backend\widgets\ListViewWidget::class,
    'itemView' => '_product-item',
    'itemOptions' => ['class' => 'sx-list-item sx-product-card-list__item'],
],
```

The collection widgets continue to own presets, filters, empty/no-results
states, pagination, per-page, summary and service/multi actions. A domain item
view owns only its content arrangement.

## 3. Compose cells and entity links

Use the existing semantic roles rather than inline typography or colors:

- `sx-collection-cell__primary` for the entity title;
- `__secondary` and `__subtle` for supporting copy;
- `__amount` for money;
- `sx-status` or `sx-text--success|warning|danger` for real state;
- `sx-preview-card` for media plus identity.

For a primary grid title, prefer the explicit column:

```php
'name' => [
    'class' => \skeeks\cms\backend\grid\BackendEntityLinkColumn::class,
    'attribute' => 'name',
    'controllerId' => '/catalog/admin-product',
],
```

For composed markup, use `BackendEntityLink::widget()` with `label` for plain
text or `content` only for deliberately trusted HTML. It always keeps a normal
`/view?pk=...` fallback and progressively opens the safe read-only `view` card
in the shared drawer. `BackendModelStandartController` supplies that card by
default. Never infer the drawer action from action order and never make delete
the entity-link action.

`DefaultActionColumn` remains a compatibility facade that derives the current
controller and model primary key. Existing consumers may keep it; new code and
cross-controller links use `BackendEntityLinkColumn` with explicit parameters.

## 4. Choose a surface

| Primitive | Use it for | Asset rule |
| --- | --- | --- |
| `BackendSurfaceWidget` | Canonical header/body/actions/footer composition for new Admin and UPA UI | Registers `BackendUiAsset` automatically |
| `sx-surface` | Direct low-level surface or special composition that cannot use the widget | Included in `BackendUiAsset` |
| `sx-block`, `sx-panel` | Existing compatibility markup only | `BackendBlockAsset` / `BackendPanelAsset`; do not emit in new UI |

Pass `title`, `hint`, `actions`, `content`, `footer`, `raised`, `clip`,
`responsive` and HTML options to `BackendSurfaceWidget`. Improve its global
contract when a reusable slot or modifier is missing; do not recreate a panel
in project CSS. Its canonical structure is `sx-surface__header`,
`sx-surface__heading`, `sx-surface__title`, `sx-surface__hint`,
`sx-surface__actions`, `sx-surface__body` and `sx-surface__footer`.
`BackendBlockAsset` and `BackendPanelAsset` remain deprecated functional
compatibility bundles for installed consumers. The UPA shell must not load
either bundle globally; the Admin shell may still load the block bundle while
its legacy views are being migrated. Do not add either asset as a dependency
of a new component.

## 5. Own optional assets

Create a narrow AssetBundle in the package that owns the component. Keep it
out of the base graph and depend on the smallest semantic layer.

```php
final class ProductCardAsset extends \skeeks\cms\base\AssetBundle
{
    public $sourcePath = '@skeeks/catalog/assets/src';
    public $css = ['product-card.css'];
    public $js = ['product-card.js'];
    public $depends = [
        \skeeks\cms\backend\assets\BackendUiAsset::class,
    ];
}
```

Surface structure is already part of `BackendUiAsset`. Heavy chart, map,
editor, upload, sortable or gallery dependencies belong to the widget or page
that needs them. Compare the rendered route with `ASSET_BUDGET.md`.

## 6. Theme and responsive rules

- Consume semantic variables; never add a fixed light-theme palette to a
  shared component.
- Keep light and dark roles equivalent. User customization is limited to the
  ten `BackendThemePalette::INPUT_KEYS`; component and compatibility tokens are
  not editor inputs.
- Use CSS Grid/flex and semantic modifiers such as `sx-surface--responsive`.
  Bootstrap utilities may remain local compatibility markup, not public API.
- Verify focus-visible, keyboard fallback, readable semantic status contrast,
  reduced motion where animation exists, and zero horizontal overflow.

## 7. Required verification

1. Run PHP lint in the project Docker container and JS syntax checks locally.
2. Exercise the changed Admin route in light and dark.
3. Exercise the corresponding UPA route when a shared contract is involved.
4. Check desktop and a narrow viewport; restore the original theme and viewport.
5. Inspect loaded CSS/JS. A conditional bundle must be absent without its
   component, and no standard route may gain Unify, jGrowl, Toastr or Slick.
6. Run the package contract tests and `git diff --check`.
7. Stage explicit intended paths only. Do not commit or push without approval.

## 8. Vendor boundary and compatibility register

Reusable UI goes to the owning shared vendor package. Project code may keep
brand token values, genuine product geometry and exceptional behavior. It must
not replace the backend shell or publish global fixes for a reusable vendor
problem. Never modernize `skeeks/crm`.

Current compatibility contracts and removal conditions:

- `DefaultActionColumn`: remove only after consumers migrate to explicit
  `BackendEntityLinkColumn` configuration.
- legacy icon classes/font asset: remove after emitted Admin/UPA markup uses
  semantic `BackendIcon` names or package-owned SVG exclusively.
- `sx-block`, `sx-panel`, `BackendBlockAsset` and `BackendPanelAsset`:
  compatibility only; keep their conditional bundles functional and remove
  them only after repository consumers emit `BackendSurfaceWidget` or
  canonical `sx-surface` structure.
- jQuery UI sortable and ContextMenu: replace consumer by consumer; never
  delete based only on one Network trace.
- `cms-theme-unify-v2`, jGrowl and other legacy bundles: explicit opt-in
  compatibility only; they must not enter the standard Admin/UPA graph.

When a compatibility adapter is introduced, document its consumers, owner and
observable removal condition in the package that owns it.
