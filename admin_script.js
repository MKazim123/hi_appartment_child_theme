jQuery(document).ready(function(){

    jQuery(document).on('change','.user_earning', function(){
        let user_id = jQuery(this).val();
        if(user_id){
            let formdata_earning = new FormData();
            formdata_earning.append('action','earning_page_admin_action');
            formdata_earning.append('user_id',user_id);
            jQuery.ajax({
                type: "post",
                data : formdata_earning,
                url: opt.ajaxUrl,
                success: function(msg){
                    jQuery('.earnings_results').html(msg);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }
    });


    jQuery(document).on('change','.user_stats', function(){
        let user_id = jQuery(this).val();
        if(user_id){
            let formdata_stats = new FormData();
            formdata_stats.append('action','stats_page_admin_action');
            formdata_stats.append('user_id',user_id);
            jQuery.ajax({
                type: "post",
                data : formdata_stats,
                url: opt.ajaxUrl,
                success: function(msg){
                    jQuery('.stats_results').html(msg);
                    let formdata_graph = new FormData();
                    formdata_graph.append('action','get_graphs_page_admin_action');
                    formdata_graph.append('user_id',user_id);
                    jQuery.ajax({
                        type: "post",
                        data : formdata_graph,
                        dataType:"json",
                        url: opt.ajaxUrl,
                        success: function(msg){
                            console.log(msg);
                            let earnings = msg['earnings'];
                            let dates = msg['dates'];
                            const data_line = {
                                labels: dates,
                                datasets: [{
                                    label: 'My Earnings',
                                    data: earnings,
                                    fill: false,
                                    borderColor: 'rgb(75, 192, 192)',
                                    tension: 0.1
                                }]
                            };
                            new Chart('line_chart', {
                                type: 'line',
                                data: data_line,
                                options: {
                                    scales: {
                                        yAxes: [{
                                            ticks: {
                                                beginAtZero: true
                                            }
                                        }]
                                    }
                                }
                            });


                            let listing_title = msg['listing_title'];
                            let listing_views = msg['listing_views'];
                            const data_bar = {
                                labels: listing_title,
                                datasets: [{
                                    label: 'Top Listing Views',
                                    data: listing_views,
                                    backgroundColor: [
                                        'rgb(255, 99, 132)',
                                        'rgb(255, 159, 64)',
                                        'rgb(255, 205, 86)',
                                        'rgb(75, 192, 192)',
                                        'rgb(54, 162, 235)',
                                        'rgb(153, 102, 255)',
                                        'rgb(201, 203, 207)',
                                        'rgb(255, 99, 132)',
                                        'rgb(255, 159, 64)',
                                        'rgb(255, 205, 86)',
                                        'rgb(255, 99, 132)',
                                        'rgb(255, 159, 64)',
                                        'rgb(255, 205, 86)',
                                        'rgb(75, 192, 192)',
                                        'rgb(54, 162, 235)',
                                        'rgb(153, 102, 255)',
                                        'rgb(201, 203, 207)',
                                        'rgb(255, 99, 132)',
                                        'rgb(255, 159, 64)',
                                        'rgb(255, 205, 86)'
                                    ],
                                    borderWidth: 0
                                }]
                            };
                            const options_bar = {
                                options: {
                                    responsive: true,
                                    aspectRatio: .5
                                },
                                plugins: {
                                    legend: {
                                        display: false,
                                    },
                                }
                            };
                            new Chart('bar_chart', {
                                type: 'doughnut',
                                options: options_bar,
                                data: data_bar
                            });
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                },
                cache: false,
                contentType: false,
                processData: false
            });
        }
    });
});