(function() {

    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('uatt_article');

    tinymce.create('tinymce.plugins.UAttArticle', {
        init : function(ed, url) {

            ed.addCommand('UAttArticleCmd', function() {
                
                var node = ed.selection.getNode();
                
                var attOpt = null;
                
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-article') !== -1
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
                    file : '/article/user.tinyplugin/article' + params,
                    width : 1000,
                    height : 400,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });  

            });

            ed.addButton('uatt_article', {
                title : 'uatt_article.button',
                cmd : 'UAttArticleCmd',
                label: 'Artykuł',
                image: url + '/img/plugin.png'
            });

            ed.onNodeChange.add(function(ed, cm, node) {
                cm.setActive('uatt_article',
                    ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-article') !== -1
                    );
            });
            
            ed.onClick.add(function(ed, e) {
                node = e.target;
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-article') !== -1
                        ) {
                    ed.execCommand('UAttArticleCmd', true, node);
                    return false;
                }
                return true;
            });
         
        },

        getInfo : function() {
            return {
                longname : 'Add user articles',
                author : 'Miroslaw Kapinos',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('uatt_article', tinymce.plugins.UAttArticle);
})();