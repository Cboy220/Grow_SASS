"use strict";

$(document).ready(function () {
    var income_chart = new Chartist.Line('.saasincome', {
        labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
        series: [
            NX.saas_home_chart_income
        ]
    }, {
        lineSmooth: Chartist.Interpolation.simple({
            divisor: 2
        }),
        showArea: true,
        low: 0,
        fullWidth: true,
        plugins: [
            Chartist.plugins.tooltip()
        ],

    });

    income_chart.on('draw', function (ctx) {
        if (ctx.type === 'area') {
            ctx.element.attr({
                x1: ctx.x1 + 0.001
            });
        }
    });
    income_chart.on('created', function (ctx) {
        var defs = ctx.svg.elem('defs');
        defs.elem('linearGradient', {
            id: 'gradient',
            x1: 0,
            y1: 1,
            x2: 0,
            y2: 0
        }).elem('stop', {
            offset: 0,
            'stop-color': 'rgba(255, 255, 255, 1)'
        }).parent().elem('stop', {
            offset: 1,
            'stop-color': 'rgba(36, 210, 181, 1)'
        });
    });
    var income_chart = [income_chart];
});