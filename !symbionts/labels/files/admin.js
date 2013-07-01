$(document).ready(function(){
    $('.symbionts-labels-admin [data-alias]').click(function(){
        var alias=$(this).attr('data-alias');
        var path=$(this).attr('data-path');
        if(Admin.tabExists(alias)){
            Admin.tabShow(alias);
        }
        else{
            Admin.tabAdd(alias, 'Loading');
            $.ajax({
                'data':{
                    'symbiont': 'Labels.change',
                    'link': $.symbiosis.link,
                    'path': path
                },
                'dataType': 'html',
                'success':function(content){
                    var html=$(content);
                    Admin.init(html);
                    html.find('.admin-button-save')
                    .click(function(){
                        var tab=$(this).closest('.admin-widget-tab').attr('data-tab');
                        var path=$(this).closest('.symbionts-labels-admin-change').attr('data-path');
                        var labels={};
                        $('.symbionts-labels-admin-change li').each(function(){
                            labels[$(this).find('label').html()]=$(this).find('input').val();
                        });
                        $.ajax({
                            'data':{
                                'symbiont': 'Labels.dbChange',
                                'path': path,
                                'labels': labels
                            },
                            'success': function(){
                                Admin.tabRemove(tab);
                            }
                        });
                        
                    });
                    html.find('.admin-button-cancel')
                    .click(function(){
                        var tab=$(this).closest('.admin-widget-tab').attr('data-tab');
                        Admin.tabRemove(tab);
                    });
                    Admin.tabSet(alias, html.find('h1').html(), html);
                    Admin.tabShow(alias);
                }
            });
        }
    });
});