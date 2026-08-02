(function () {
    'use strict';

    var triggerSelector = '[data-sx-shell-menu-target]';

    function resolveTarget(trigger) {
        var selector = trigger.getAttribute('data-sx-shell-menu-target');

        if (!selector || selector.charAt(0) !== '#') {
            return null;
        }

        return document.getElementById(selector.slice(1));
    }

    function syncTrigger(trigger) {
        var target = resolveTarget(trigger);
        var item = trigger.closest('.sx-shell-menu__item');
        var isOpen = Boolean(item && (
            item.classList.contains('sx-shell-menu__item--open')
            || item.classList.contains('sx-shell-menu__item--active')
        ));

        trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (target && target.id) {
            trigger.setAttribute('aria-controls', target.id);
        }
    }

    function toggleMenu(trigger) {
        var target = resolveTarget(trigger);
        var item = trigger.closest('.sx-shell-menu__item');

        if (!target || !item) {
            return false;
        }

        var isOpen = item.classList.toggle('sx-shell-menu__item--open');
        trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        return true;
    }

    function init() {
        document.querySelectorAll(triggerSelector).forEach(syncTrigger);
    }

    document.addEventListener('click', function (event) {
        var trigger = event.target.closest(triggerSelector);

        if (!trigger || !toggleMenu(trigger)) {
            return;
        }

        event.preventDefault();
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
