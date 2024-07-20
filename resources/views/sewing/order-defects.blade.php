@extends('layouts.index')

@section('content')
    <div class="container-fluid">
        <h3 class="my-3 text-sb text-center fw-bold">Pareto Chart</h3>
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-3 justify-content-between mb-3">
                    <div>
                        <label>Buyer</label>
                        <select class="form-select form-select-sm" name="supplier" id="supplier">
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-3">
                        <div>
                            <label>Dari</label>
                            <input class="form-control form-control-sm" type="date" id="date-from">
                        </div>
                        <div>
                            <label>Sampai</label>
                            <input class="form-control form-control-sm" type="date" id="date-to">
                        </div>
                    </div>
                </div>
                <div id="chart"></div>
            </div>
        </div>
    </div>
@endsection

@section('custom-script')
    <script>
        function autoBreak(label) {
            const maxLength = 5;
            const lines = [];

            if (label) {
                for (let word of label.split(" ")) {
                    if (lines.length == 0) {
                        lines.push(word);
                    } else {
                        const i = lines.length - 1
                        const line = lines[i]

                        if (line.length + 1 + word.length <= maxLength) {
                            lines[i] = `${line} ${word}`
                        } else {
                            lines.push(word)
                        }
                    }
                }
            }

            return lines;
        }

        document.addEventListener('DOMContentLoaded', () => {
            // bar chart options
            var options = {
                chart: {
                    height: 550,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        dataLabels: {
                            position: 'top',
                        },
                        colors: {
                            ranges: [
                                {
                                    from: 500,
                                    to: 99999,
                                    color: '#333'
                                },{
                                    from: 60,
                                    to: 499,
                                    color: '#d33141'
                                },{
                                    from: 30,
                                    to: 59,
                                    color: '#ff971f'
                                },{
                                    from: 0,
                                    to: 15,
                                    color: '#12be60'
                                }
                            ],
                            backgroundBarColors: [],
                            backgroundBarOpacity: 1,
                            backgroundBarRadius: 0,
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#333']
                    },
                    formatter: function (val, opts) {
                        return val.toLocaleString()
                    },
                    offsetY: -30
                },
                series: [],
                xaxis: {
                    labels: {
                        show: true,
                        rotate: 0,
                        rotateAlways: false,
                        hideOverlappingLabels: false,
                        showDuplicates: false,
                        trim: false,
                        minHeight: undefined,
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fontWeight: 600,
                            cssClass: 'apexcharts-xaxis-label',
                        },
                    }
                },
                title: {
                    text: 'Supplier',
                    align: 'center',
                    style: {
                        fontSize:  '18px',
                        fontWeight:  'bold',
                        fontFamily:  undefined,
                        color:  '#263238'
                    },
                },
                noData: {
                    text: 'Loading...'
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#chart"),
                options
            );
            chart.render();

            // fetch order defect data function
            function getOrderDefectData(idSupplier, namaSupplier, dari, sampai) {
                $.ajax({
                    url: '{{ url('order-defects') }}/'+idSupplier+'/'+dari+'/'+sampai,
                    type: 'get',
                    dataType: 'json',
                    success: function(res) {
                        let totalDefect = 0;
                        let dataArr = [];
                        res.forEach(element => {
                            totalDefect += element.total_defect;
                            dataArr.push({'x' : autoBreak(element.defect_type), 'y' : element.total_defect });
                        });

                        chart.updateSeries([{
                            data: dataArr
                        }], true);

                        chart.updateOptions({
                            title: {
                                text: namaSupplier,
                                align: 'center',
                                style: {
                                    fontSize:  '18px',
                                    fontWeight:  'bold',
                                    fontFamily:  undefined,
                                    color:  '#263238'
                                },
                            },
                            subtitle: {
                                text: [dari+' / '+sampai, 'Total Defect : '+totalDefect.toLocaleString()],
                                align: 'center',
                                style: {
                                    fontSize:  '13px',
                                    fontFamily:  undefined,
                                    color:  '#263238'
                                },
                            }
                        });
                    }, error: function (jqXHR) {
                        let res = jqXHR.responseJSON;
                        console.error(res.message);
                        iziToast.error({
                            title: 'Error',
                            message: res.message,
                            position: 'topCenter'
                        });
                    }
                });
            }

            function updateBuyerList() {
                $.ajax({
                    url: 'order-defects',
                    type: 'get',
                    data: {
                        dateFrom : $('#date-from').val(),
                        dateTo : $('#date-to').val(),
                    },
                    success: function(res) {
                        // Clear options
                        $("#supplier").html("");

                        res.forEach((element, index) => {
                            console.log(element, index);
                            if ($('#supplier').find("option[value='"+element.id+"']").length) {
                                $('#supplier').val(element.id);
                            } else {
                                // Create a DOM Option and pre-select by default
                                var newOption = new Option(element.name, element.id, true, true);
                                // Append it to the select
                                if (index == 0) {
                                    $('#supplier').append(newOption).trigger('change');
                                } else {
                                    $('#supplier').append(newOption);
                                }
                            }
                        });
                    },
                    error: function (jqXHR) {
                        let res = jqXHR.responseJSON;
                        console.error(res.message);
                        iziToast.error({
                            title: 'Error',
                            message: res.message,
                            position: 'topCenter'
                        });
                    }
                });
            }

            // select2
            $('#supplier').select2({
                theme: "bootstrap-5",
            });

            // initial fetch

            let today = new Date();
            let todayDate = ("0" + today.getDate()).slice(-2);
            let todayMonth = ("0" + (today.getMonth() + 1)).slice(-2);
            let todayYear = today.getFullYear();
            let todayFull = todayYear+'-'+todayMonth+'-'+todayDate;
            let twoWeeksBefore = new Date(new Date().setDate(new Date().getDate() - 14));
            let twoWeeksBeforeDate = ("0" + twoWeeksBefore.getDate()).slice(-2);
            let twoWeeksBeforeMonth = ("0" + (twoWeeksBefore.getMonth() + 1)).slice(-2);
            let twoWeeksBeforeYear = twoWeeksBefore.getFullYear();
            let twoWeeksBeforeFull = twoWeeksBeforeYear+'-'+twoWeeksBeforeMonth+'-'+twoWeeksBeforeDate;
            $('#date-to').val(todayFull);
            $('#date-from').val(twoWeeksBeforeFull);

            getOrderDefectData($('#supplier').val(), $('#supplier option:selected').text(), $('#date-from').val(), $('#date-to').val())

            // fetch on select supplier
            $('#supplier').on('select2:select', function (e) {
                getOrderDefectData(e.params.data.element.value, e.params.data.element.innerText, $('#date-from').val(), $('#date-to').val());
            });

            // fetch on select date
            $('#date-from').change(function (e) {
                updateBuyerList();
                getOrderDefectData($('#supplier').val(), $('#supplier option:selected').text(), $('#date-from').val(), $('#date-to').val());
            });

            $('#date-to').change(function (e) {
                updateBuyerList();
                getOrderDefectData($('#supplier').val(), $('#supplier option:selected').text(), $('#date-from').val(), $('#date-to').val());
            });

            // fetch every 30 second
            setInterval(function(){
                getOrderDefectData($('#supplier').val(), $('#supplier option:selected').text(), $('#date-from').val(), $('#date-to').val());
            }, 30000)
        });

    </script>
@endsection
