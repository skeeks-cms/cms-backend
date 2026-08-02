/*!
 * Standard backend window adapter.
 */
(function (sx) {
    "use strict";

    if (!sx.classes || !sx.classes.BackendWindow) {
        throw new Error("BackendWindowAsset must be loaded before its compatibility adapter.");
    }

    sx.classes.Window = sx.classes.BackendWindow.extend({});
})(sx);
