/*! Icinga Web 2 | (c) 2015 Icinga Development Team | GPLv2+ */

(function(Icinga) {

    var Windows = function(module) {
        this.module = module;
        this.initialize();
        this.module.icinga.logger.debug('Windows module loaded');
    };

    Windows.prototype = {

        initialize: function()
        {
            this.module.icinga.logger.debug('Windows module initialized');
        }
    };

    Icinga.availableModules.windows = Windows;

}(Icinga));

