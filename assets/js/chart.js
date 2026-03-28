/**
 * @param {string} property
 * @return {string}
 */
function getHexColorByCssProperty(property) {
    const color = getComputedStyle(document.documentElement).getPropertyValue(property).trim();

    const ctx = document.createElement('canvas').getContext('2d');
    ctx.fillStyle = color;
    ctx.fillRect(0, 0, 1, 1);

    const [r, g, b] = ctx.getImageData(0, 0, 1, 1).data;

    return "#" + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('');
}

/**
 * @param {string} dateValue
 * @return {number}
 */
function getSecondsInMonth(dateValue) {
    const date = new Date(dateValue);
    const start = new Date(date.getFullYear(), date.getMonth(), 1, 0, 0, 0);
    const startNext = new Date(date.getFullYear(), date.getMonth() + 1, 1, 0, 0, 0);

    return Math.floor((startNext - start) / 1000);
}

class DimensionCalculator {
    /**
     @param {number} values
     * @return {number}
     */
    #getMin(...values) {
        return Math.floor(Math.min(...values) * 10) / 10;
    }

    /**
     @param {number} values
     * @return {number}
     */
    #getMax(...values) {
        return Math.ceil(Math.max(...values) * 10) / 10;
    }

    /**
     * @param {number} min
     * @param {number} max
     * @param {number} coefficient
     * @return {{min: number, max: number}}
     */
    getExpandedBoundaries(min, max, coefficient) {
        if (min === max) {
            min *= 0.95;
        }

        const diff = +(max - min).toFixed(1);
        const additionalDiff = +(diff * coefficient).toFixed(1);

        return {
            min: +(min - additionalDiff).toFixed(1),
            max: +(max + additionalDiff).toFixed(1),
        }
    }


    /**
     * @param {Array<number>} values
     * @param {number} coefficient
     * @return {{min: number, max: number, interval: number}}
     */
    getBoundariesByValues(values, coefficient) {
        const boundaries = this.getExpandedBoundaries(
            this.#getMin(...values),
            this.#getMax(...values),
            coefficient
        );

        return this.#getBoundaries(boundaries.min, boundaries.max);
    }

    /**
     * @param {number} min
     * @param {number} max
     * @return {{min: number, max: number, interval: number}}
     */
    #getBoundaries(min, max) {
        let difference = +(max - min).toFixed(1) * 10;
        let interval = 1;

        if (difference > 1) {
            if (difference % 3 !== 0 && difference % 2 !== 0) {
                return this.#getBoundaries(+(min - 0.1).toFixed(1), max);
            }

            if (difference % 3 === 0) {
                interval = +(difference / 30).toFixed(1);
            } else if (difference % 4 === 0) {
                interval = +(difference / 40).toFixed(1);
            } else {
                interval = +(difference / 20).toFixed(1);
            }
        }

        return {
            min: min,
            max: max,
            interval: interval
        };
    }
}

/**
 * @param {HTMLElement} element
 * @param {Object} options
 * @param {Function} listenerOptions
 */
function initChart(element, options, listenerOptions) {
    const myChart = echarts.init(element);
    myChart.setOption(options);

    window.addEventListener('resize', () => {
        myChart.resize();
    });

    document.addEventListener('app:root-data:theme', () => {
        myChart.setOption(listenerOptions());
    });

    document.addEventListener('app:root-data:sidebar', () => {
        myChart.resize();
    });
}

/**
 * @param {HTMLElement} element
 * @param {Array<{type: string, prev_time: number, time: number, fat: number}>} data
 */
function initMeasurementWeightChart(element, data) {
    const calculator = new DimensionCalculator();
    const xAxis = calculator.getExpandedBoundaries(
        0,
        element.dataset.days ? element.dataset.days * 86400 : getSecondsInMonth(element.dataset.date),
        0.02
    );
    const yAxis = calculator.getBoundariesByValues(data.map(item => item.fat), 0.1);

    const redColor = getHexColorByCssProperty('--color-red');
    const greenColor = getHexColorByCssProperty('--color-green');

    initChart(
        element,
        {
            animationDuration: 200,
            grid: {left: 0, bottom: 0, top: 0, right: 0},
            xAxis: {
                min: xAxis.min,
                max: xAxis.max,
                axisLine: false,
                axisLabel: false,
                splitLine: {show: false}
            },
            yAxis: {
                min: yAxis.min,
                max: yAxis.max,
                interval: yAxis.interval,
                axisLine: false,
                axisLabel: {
                    fontSize: 10,
                    formatter: '{value} {percentStyle|%}',
                    rich: {percentStyle: {fontSize: 8, opacity: 0.15}},
                    color: getHexColorByCssProperty('--color'),
                    opacity: 0.2,
                },
                splitLine: {
                    show: true,
                    lineStyle: {
                        color: getHexColorByCssProperty('--color'),
                        opacity: 0.04,
                    }
                },
            },
            visualMap: {
                show: false,
                dimension: 0,
                pieces: data.map(item => {
                    return {
                        gt: item.prev_time ?? null,
                        lte: item.time,
                        color: item.type === 'increased' ? redColor : greenColor
                    };
                }),
            },
            series: [{
                type: 'line',
                data: data.map(item => [item.time, item.fat]),
                smooth: true,
                showSymbol: element.dataset.symbols !== 'hide',
                symbol: 'circle',
                areaStyle: {
                    opacity: 0.1,
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {offset: 0, color: greenColor},
                        {offset: 0.3, color: greenColor},
                        {offset: 1, color: 'transparent'}
                    ])
                },
            }]
        },
        function () {
            return {
                yAxis: {
                    axisLabel: {color: getHexColorByCssProperty('--color')},
                    splitLine: {lineStyle: {color: getHexColorByCssProperty('--color')}},
                }
            };
        }
    );
}

/**
 * @param {HTMLElement} element
 * @param {Array<{type: string, color: string, prev_time: number, time: number}>} data
 */
function initMoodReflectionsChart(element, data) {
    const calculator = new DimensionCalculator();
    const xAxis = calculator.getExpandedBoundaries(
        0,
        element.dataset.days * 86400,
        0.01
    );

    let values = [];
    for (const value of data) {
        values[value.color] = {color: value.color, value: value.type};
    }

    initChart(
        element,
        {
            animationDuration: 200,
            grid: {left: 0, bottom: 4, top: 4, right: 0},
            xAxis: {
                min: xAxis.min,
                max: xAxis.max,
                axisLine: false,
                axisLabel: false,
                splitLine: {show: false},
            },
            yAxis: {
                min: 1,
                max: 5,
                interval: 1,
                axisLine: false,
                axisLabel: false,
                splitLine: {
                    show: true,
                    lineStyle: {
                        color: getHexColorByCssProperty('--color'),
                        opacity: 0.04,
                    }
                },
            },
            visualMap: {
                show: false,
                pieces: Object.values(values).map(value => {
                    return {
                        gt: value.value - 0.95,
                        lte: value.value + 0.05,
                        color: getHexColorByCssProperty('--color-' + value.color)
                    }
                }),
            },
            series: [{
                type: 'line',
                data: data.map(item => [item.time, item.type]),
                smooth: true,
                symbol: 'circle',
                areaStyle: {
                    opacity: 0.1,
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {offset: 0, color: getHexColorByCssProperty('--color-green')},
                        {offset: 0.3, color: getHexColorByCssProperty('--color-green')},
                        {offset: 1, color: 'transparent'}
                    ])
                },
            }]
        },
        function () {
            return {
                yAxis: {
                    axisLabel: {color: getHexColorByCssProperty('--color')},
                    splitLine: {lineStyle: {color: getHexColorByCssProperty('--color')}},
                }
            };
        }
    );
}

/**
 * @param {HTMLElement} element
 * @param {Array<{score: number, date: number}>} data
 */
function initExercisesChart(element, data) {
    const colorName = '--color-' + element.dataset.color;
    const currentTime = new Date().getTime() / 1000;

    const calculator = new DimensionCalculator();
    const xAxis = calculator.getExpandedBoundaries(
        currentTime - element.dataset.days * 86400,
        currentTime,
        0
    );
    const yAxis = calculator.getBoundariesByValues(data.map(item => item.score), 0.1);

    initChart(
        element,
        {
            animationDuration: 200,
            color: getHexColorByCssProperty(colorName),
            grid: {left: 4, bottom: 4, top: 4, right: 4},
            xAxis: {
                min: xAxis.min,
                max: xAxis.max,
                axisLine: false,
                axisLabel: {
                    fontSize: 10,
                    formatter: function (value) {
                        const date = new Date(value * 1000);

                        return date.getDate() + ' ' + date.toLocaleString('en-US', {month: 'short'});
                    },
                    color: getHexColorByCssProperty('--color'),
                    opacity: 0.2,
                },
                splitLine: {show: false},
            },
            yAxis: {
                min: yAxis.min,
                max: yAxis.max,
                interval: yAxis.interval,
                axisLine: false,
                axisLabel: false,
                splitLine: {
                    show: true,
                    lineStyle: {
                        color: getHexColorByCssProperty('--color'),
                        opacity: 0.04,
                    }
                },
            },
            series: [{
                type: 'line',
                data: data.map(item => [item.date, item.score]),
                itemStyle: {opacity: 0.6},
                lineStyle: {opacity: 0.2},
                smooth: true,
                symbol: 'circle',
            }]
        },
        function () {
            return {
                color: getHexColorByCssProperty(colorName),
                xAxis: {axisLabel: {color: getHexColorByCssProperty('--color')}},
                yAxis: {splitLine: {lineStyle: {color: getHexColorByCssProperty('--color')}}},
            };
        }
    );
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-chart]').forEach((element) => {
        if (element.dataset.chart === 'weight-change') {
            initMeasurementWeightChart(element, JSON.parse(element.dataset.values));
        } else if (element.dataset.chart === 'mood-reflections') {
            initMoodReflectionsChart(element, JSON.parse(element.dataset.values));
        } else if (element.dataset.chart === 'exercise-progress') {
            initExercisesChart(element, JSON.parse(element.dataset.values));
        }
    })
});
