(function() {

    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('imageschoose');

    tinymce.create('tinymce.plugins.ImagesChoose', {
        init : function(ed, url) {

            ed.addCommand('ImagesChooseCmd', function() {
                
                var node = ed.selection.getNode();
                if (node.nodeName.toLowerCase() == 'a' && ed.dom.getAttrib(node, 'class').indexOf('imageschooseObject') !== -1) {
                    window.globalRelatedNode = node;
                }
                else {
                    window.globalRelatedNode = null;
                }
                ed.windowManager.open({
                    file : '/attachment/tinyplugin/choose/poplay/1/type/image',
                    width : 1000,
                    height : 400,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });
            });

            ed.addButton('imageschoose', {
                title : 'imageschoose.button',
                cmd : 'ImagesChooseCmd',
                image: url + '/img/imageschoose.gif'
            });

            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('imageschoose',
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
    tinymce.PluginManager.add('imageschoose', tinymce.plugins.ImagesChoose);
})();