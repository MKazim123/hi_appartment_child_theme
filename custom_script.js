jQuery(document).ready(function(){

    jQuery(':input[type="number"]').attr({"min" : '0'});

    // const data_bar = {
    // labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
    // datasets: [{
    //     label: 'My First Dataset',
    //     data: [65, 59, 80, 81, 56, 55, 40],
    //     backgroundColor: [
    //     'rgba(255, 99, 132, 0.2)',
    //     'rgba(255, 159, 64, 0.2)',
    //     'rgba(255, 205, 86, 0.2)',
    //     'rgba(75, 192, 192, 0.2)',
    //     'rgba(54, 162, 235, 0.2)',
    //     'rgba(153, 102, 255, 0.2)',
    //     'rgba(201, 203, 207, 0.2)'
    //     ],
    //     borderColor: [
    //     'rgb(255, 99, 132)',
    //     'rgb(255, 159, 64)',
    //     'rgb(255, 205, 86)',
    //     'rgb(75, 192, 192)',
    //     'rgb(54, 162, 235)',
    //     'rgb(153, 102, 255)',
    //     'rgb(201, 203, 207)'
    //     ],
    //     borderWidth: 1
    // }]
    // };
    // const options_bar = {
    //     type: 'bar',
    //     options: {
    //         scales: {
    //         y: {
    //             beginAtZero: true
    //         }
    //         }
    //     },
    // };
    // new Chart('bar_chart', {
    //     type: 'bar',
    //     options: options_bar,
    //     data: data_bar
    // });





    let formdata_earning = new FormData();
    formdata_earning.append('action','earning_chart_front_action');
    jQuery.ajax({
        type: "post",
        data : formdata_earning,
        dataType:"json",
        url: opt.ajaxUrl,
        success: function(msg){
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
        },
        cache: false,
        contentType: false,
        processData: false
    });




    let formdata_views = new FormData();
    formdata_views.append('action','views_chart_front_action');
    jQuery.ajax({
        type: "post",
        data : formdata_views,
        dataType:"json",
        url: opt.ajaxUrl,
        success: function(msg){
            let listing_title = msg['listing_title'];
            let listing_views = msg['listing_views'];
            let empty_labels = [];
            listing_views.forEach(element => {
                empty_labels.push('');
            });
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


     
    // const data_line = {
    // labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
    // datasets: [{
    //     label: 'My First Dataset',
    //     data: [65, 59, 80, 81, 56, 55, 40],
    //     fill: false,
    //     borderColor: 'rgb(75, 192, 192)',
    //     tension: 0.1
    // }]
    // };
    // // const options_line = {
    // //     type: 'line',
    // //     data: data,
    // // };

    // new Chart('line_chart', {
    //     type: 'line',
    //     data: data_line
    // });
    


    jQuery(document).on('click', '.heateor_sss_sharing_ul a', function(){
        let sharing_platform = jQuery(this).attr('title');
        let listing_url = jQuery(this).parent().parent().attr('data-heateor-sss-href');
        let formdata = new FormData();
        formdata.append('action','social_earning_action');
        formdata.append('sharing_platform',sharing_platform);
        formdata.append('listing_url',listing_url);
        jQuery.ajax({
            type: "post",
            data : formdata,
            url: opt.ajaxUrl,
            success: function(msg){
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });
});