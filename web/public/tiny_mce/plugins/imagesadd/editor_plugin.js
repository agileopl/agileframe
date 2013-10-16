(function() {

    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('imagesadd');

    tinymce.create('tinymce.plugins.ImagesAdd', {
        init : function(ed, url) {

            ed.addCommand('ImagesAddCmd', function() {
                
                var node = ed.selection.getNode();
                if (node.nodeName.toLowerCase() == 'a' && ed.dom.getAttrib(node, 'class').indexOf('imagesaddObject') !== -1) {
                    window.globalRelatedNode = node;
                }
                else {
                    window.globalRelatedNode = null;
                }
                ed.windowManager.open({
                    file : '/attachment/index/upload.single/poplay/1/type/image/redirect/L2F0dGFjaG1lbnQvdGlueXBsdWdpbi9pbnNlcnQvcG9wbGF5LzE=',
                    width : 1000,
                    height : 400,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });
            });

            ed.addButton('imagesadd', {
                title : 'imagesadd.button',
                cmd : 'ImagesAddCmd',
                image: url + '/img/imagesadd.gif'
            });

            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('imagesadd',
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
    tinymce.PluginManager.add('imagesadd', tinymce.plugins.ImagesAdd);
})();