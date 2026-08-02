/*!
 * Backend defaults for the shared native sx.block implementation.
 */
(function (sx, $, _) {
    "use strict";

    sx.createNamespace("classes", sx);

    if (!sx.classes.BlockerNative) {
        throw new Error("Backend blocker requires sx.classes.BlockerNative");
    }

    sx.classes.BackendBlocker = sx.classes.BlockerNative.extend({
        _init: function () {
            this.defaultOpts({
                ariaLabel: sx.Config.get("blocker_wait_text") || "Loading",
                delay: 80,
                minDuration: 180,
                shimmer: true,
                showSpinner: true
            });

            this.applyParentMethod(sx.classes.BlockerNative, "_init", []);
        }
    });

    sx.classes.Blocker = sx.classes.BackendBlocker.extend({});
})(sx, sx.$, sx._);
