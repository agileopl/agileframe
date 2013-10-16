(function() {

    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('uatt_quiz');

    tinymce.create('tinymce.plugins.UAttQuiz', {
        init : function(ed, url) {

            ed.addCommand('UAttQuizCmd', function() {
                
                var node = ed.selection.getNode();
                
                var attOpt = null;
                
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-quiz') !== -1
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
                    file : '/quiz/user.tinyplugin/quiz' + params,
                    width : 1000,
                    height : 400,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });  

            });

            ed.addButton('uatt_quiz', {
                title : 'uatt_quiz.button',
                cmd : 'UAttQuizCmd',
                label: 'Quiz',
                image: url + '/img/plugin.png'
            });

            ed.onNodeChange.add(function(ed, cm, node) {
                cm.setActive('uatt_quiz',
                    ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-quiz') !== -1
                    );
            });
            
            ed.onClick.add(function(ed, e) {
                node = e.target;
                if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-quiz') !== -1
                        ) {
                    ed.execCommand('UAttQuizCmd', true, node);
                    return false;
                }
                return true;
            });
         
        },

        getInfo : function() {
            return {
                longname : 'Add user quizs',
                author : 'Miroslaw Kapinos',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('uatt_quiz', tinymce.plugins.UAttQuiz);
})();