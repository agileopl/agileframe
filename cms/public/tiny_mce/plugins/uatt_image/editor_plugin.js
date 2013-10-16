(function() {

    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('uatt_image');

    tinymce.create('tinymce.plugins.UAttImage', {
        init : function(ed, url) {

            ed.addCommand('UAttImageCmd', function() {
                
                var node = ed.selection.getNode();
                
                var attOpt = null;
                
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-image') !== -1
                        ) {
                    
                    attOpt = {
                        id: ed.dom.getAttrib(node, 'data-id'),
                        type: ed.dom.getAttrib(node, 'data-type'),
                        align: ed.dom.getAttrib(node, 'data-align')
                    };
                    
                }
                
                params = '';
                if(attOpt) {
                    params = '/id/'+attOpt.id+'/align/'+attOpt.align;
                }
                
                ed.windowManager.open({
                    file : '/attachment/user.tinyplugin/image' + params,
                    width : 1000,
                    height : 400,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });  

            });

            ed.addButton('uatt_image', {
                title : 'uatt_image.button',
                cmd : 'UAttImageCmd',
                label: 'Zdjęcia',
                image: url + '/img/plugin.png'
            });

            ed.onNodeChange.add(function(ed, cm, node) {
                cm.setActive('uatt_image',
                    ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-image') !== -1
                    );
            });
            
            ed.onClick.add(function(ed, e) {
                node = e.target;
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-image') !== -1
                        ) {
                    ed.execCommand('UAttImageCmd', true, node);
                    return false;
                }
                return true;
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
    tinymce.PluginManager.add('uatt_image', tinymce.plugins.UAttImage);
})();