(function() {

    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('uatt_file');

    tinymce.create('tinymce.plugins.UAttFile', {
        init : function(ed, url) {

            ed.addCommand('UAttFileCmd', function() {
                
                var node = ed.selection.getNode();
                
                var attOpt = null;
                
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-file') !== -1
                        ) {
                    
                    attOpt = {
                        id: ed.dom.getAttrib(node, 'data-id'),
                        type: ed.dom.getAttrib(node, 'data-type')
                    };
                    
                }
                
                params = '';
                if(attOpt) {
                    params = '/id/'+attOpt.id;
                }
                
                ed.windowManager.open({
                    file : '/attachment/user.tinyplugin/file' + params,
                    width : 1000,
                    height : 400,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });  

            });

            ed.addButton('uatt_file', {
                title : 'uatt_file.button',
                cmd : 'UAttFileCmd',
                label: 'Plik',
                image: url + '/img/plugin.png'
            });

            ed.onNodeChange.add(function(ed, cm, node) {
                cm.setActive('uatt_file',
                    ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-file') !== -1
                    );
            });
            
            ed.onClick.add(function(ed, e) {
                node = e.target;
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-file') !== -1
                        ) {
                    ed.execCommand('UAttFileCmd', true, node);
                    return false;
                }
                return true;
            });
         
        },

        getInfo : function() {
            return {
                longname : 'Add user file',
                author : 'Miroslaw Kapinos',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('uatt_file', tinymce.plugins.UAttFile);
})();