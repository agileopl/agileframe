(function() {

    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('uatt_video');

    tinymce.create('tinymce.plugins.UAttVideo', {
        init : function(ed, url) {

            ed.addCommand('UAttVideoCmd', function() {
                
                var node = ed.selection.getNode();
                
                var attOpt = null;
                
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-video') !== -1
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
                    file : '/attachment/user.tinyplugin/video' + params,
                    width : 1000,
                    height : 400,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });  

            });

            ed.addButton('uatt_video', {
                title : 'uatt_video.button',
                cmd : 'UAttVideoCmd',
                label: 'Video',
                image: url + '/img/plugin.png'
            });

            ed.onNodeChange.add(function(ed, cm, node) {
                cm.setActive('uatt_video',
                    ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-video') !== -1
                    );
            });
            
            ed.onClick.add(function(ed, e) {
                node = e.target;
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-video') !== -1
                        ) {
                    ed.execCommand('UAttVideoCmd', true, node);
                    return false;
                }
                return true;
            });
         
        },

        getInfo : function() {
            return {
                longname : 'Add user videos',
                author : 'Miroslaw Kapinos',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('uatt_video', tinymce.plugins.UAttVideo);
})();