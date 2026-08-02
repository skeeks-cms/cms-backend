(function () {
    'use strict';

    var toggleSelector = '[data-sx-shell-nav-toggle]';
    var backdropSelector = '[data-sx-shell-nav-backdrop]';
    var openClass = 'sx-shell-nav-open';

    function getTarget(toggle) {
        var targetId = toggle.getAttribute('aria-controls') || 'sideNav';

        return document.getElementById(targetId);
    }

    function setExpanded(isOpen) {
        document.body.classList.toggle(openClass, isOpen);

        document.querySelectorAll(toggleSelector).forEach(function (toggle) {
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            var target = getTarget(toggle);
            if (target) {
                target.classList.toggle('active', isOpen);
            }
        });
    }

    function toggleNavigation(toggle) {
        var isOpen = !document.body.classList.contains(openClass);

        setExpanded(isOpen);
        if (isOpen) {
            var target = getTarget(toggle);
            if (target) {
                target.focus({preventScroll: true});
            }
        }
    }

    document.addEventListener('click', function (event) {
        var toggle = event.target.closest(toggleSelector);
        if (toggle) {
            event.preventDefault();
            toggleNavigation(toggle);
            return;
        }

        if (event.target.closest(backdropSelector)) {
            setExpanded(false);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && document.body.classList.contains(openClass)) {
            setExpanded(false);
        }
    });
}());
