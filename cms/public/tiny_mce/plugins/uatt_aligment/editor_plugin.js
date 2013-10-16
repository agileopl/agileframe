(function() {

    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('uatt_aligment');

    tinymce.create('tinymce.plugins.UAttAligment', {
        init : function(ed, url) {

            ed.addCommand('UAttAligmentCmd', function() {
                
                var node = ed.selection.getNode();
                
                var attOpt = null;
                
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-image') !== -1
                        && ed.dom.getAttrib(node, 'data-align').indexOf('gallery') === -1
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
                    file : '/attachment/user.tinyplugin/aligment' + params,
                    width : 1000,
                    height : 400,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });  

            });

            ed.addButton('uatt_aligment', {
                title : 'uatt_aligment.button',
                cmd : 'UAttAligmentCmd',
                image: url + '/img/plugin.gif'
            });

            ed.onNodeChange.add(function(ed, cm, node) {
                cm.setActive('uatt_aligment',
                    ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-image') !== -1
                        && ed.dom.getAttrib(node, 'data-align').indexOf('gallery') === -1
                    );
            });
            
            ed.onClick.add(function(ed, e) {
                node = e.target;
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-image') !== -1
                        && ed.dom.getAttrib(node, 'data-align').indexOf('gallery') === -1
                        ) {
                    ed.execCommand('UAttAligmentCmd', true, node);
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
    tinymce.PluginManager.add('uatt_aligment', tinymce.plugins.UAttAligment);
})();