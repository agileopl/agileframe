(function() {

    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('attachchoose');

    tinymce.create('tinymce.plugins.AttachChoose', {
        init : function(ed, url) {

            ed.addCommand('AttachChooseCmd', function() {
                
                var node = ed.selection.getNode();
                if (node.nodeName.toLowerCase() == 'a' && ed.dom.getAttrib(node, 'class').indexOf('attachchooseObject') !== -1) {
                    window.globalRelatedNode = node;
                }
                else {
                    window.globalRelatedNode = null;
                }
                ed.windowManager.open({
                    file : '/attachment/tinyplugin/choose/poplay/1',
                    width : 1000,
                    height : 400,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });
            });

            ed.addButton('attachchoose', {
                title : 'attachchoose.button',
                cmd : 'AttachChooseCmd',
                image: url + '/img/attachchoose.gif'
            });

            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('attachchoose',
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
    tinymce.PluginManager.add('attachchoose', tinymce.plugins.AttachChoose);
})();