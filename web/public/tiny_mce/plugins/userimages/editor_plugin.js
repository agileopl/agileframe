(function() {

    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('userimages');

    tinymce.create('tinymce.plugins.UserImages', {
        init : function(ed, url) {

            ed.addCommand('UserImagesCmd', function() {
                
                var node = ed.selection.getNode();
                if (node.nodeName.toLowerCase() == 'a' && ed.dom.getAttrib(node, 'class').indexOf('userimagesObject') !== -1) {
                    window.globalRelatedNode = node;
                }
                else {
                    window.globalRelatedNode = null;
                }
                ed.windowManager.open({
                    file : '/attachment/tinyplugin/index/poplay/1',
                    width : 1000,
                    height : 400,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });
            });

            ed.addButton('userimages', {
                title : 'userimages.button',
                cmd : 'UserImagesCmd',
                image: url + '/img/userimages.gif'
            });

            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('userimages',
                    ed.dom.getAttrib(n, 'class') &&
                    ed.dom.getAttrib(n, 'class').indexOf('uimg') !== -1 &&
                    ed.dom.getAttrib(n, 'class').indexOf(typeRef) !== -1
                    );
            });
         
        },

        getInfo : function() {
            return {
                longname : 'Add user images',
                author : 'Miroslaw Kapinos',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('userimages', tinymce.plugins.UserImages);
})();