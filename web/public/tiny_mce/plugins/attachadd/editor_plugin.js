(function() {

    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('attachadd');

    tinymce.create('tinymce.plugins.AttachAdd', {
        init : function(ed, url) {

            ed.addCommand('AttachAddCmd', function() {
                
                var node = ed.selection.getNode();
                if (node.nodeName.toLowerCase() == 'a' && ed.dom.getAttrib(node, 'class').indexOf('attachaddObject') !== -1) {
                    window.globalRelatedNode = node;
                }
                else {
                    window.globalRelatedNode = null;
                }
                ed.windowManager.open({
                    file : '/attachment/index/upload.single/poplay/1/redirect/L2F0dGFjaG1lbnQvdGlueXBsdWdpbi9pbnNlcnQvcG9wbGF5LzE=',
                    width : 1000,
                    height : 400,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });
            });

            ed.addButton('attachadd', {
                title : 'attachadd.button',
                cmd : 'AttachAddCmd',
                image: url + '/img/attachadd.gif'
            });

            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('attachadd',
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
    tinymce.PluginManager.add('attachadd', tinymce.plugins.AttachAdd);
})();