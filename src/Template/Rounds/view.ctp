<?php
/**
 * Created by PhpStorm.
 * User: rom
 * Date: 10/24/17
 * Time: 11:05 AM
 */?>

<div id="div-round-view-content" class="row">
    <div class="large-12 columns content">

        <?php foreach( $questions as $question):

            echo '<div class="row">';
                echo '<h3>'.$question->description.'</h3>';
                echo '<div class="large-12 columns">';
                echo '<div class="row">';
                echo '<div class="large-6 columns">';


                echo $this->Form->create(null,[ 'url' => ['controller' => 'answers', 'action' => 'add'],'id' => 'form-round']);

                $index = 0;
                    foreach($question['questions_indicators'] as $question_indicator):

                        if($question_indicator->target):
                            // row by indicator

                            echo '<div class="row">';
                            echo '<h4>'.$question_indicator->indicator->description.'</h4>';
                            echo '<div class="row">';

                            foreach( $question_indicator['questions_indicators_years'] as $question_indicator_year):

                                //create answer id input
                                /*echo $this->Form->control('id');
                                echo $this->Form->control('round_question_indicator_year_id')*/

                                foreach ($question_indicator_year['rounds'] as $round_question_indicator_year):
                                    echo $this->Form->control($index.'.id',['type' => 'hidden']);
                                    echo $this->Form->control($index.'.round_question_indicator_year_id',['type' => 'hidden','value' => $round_question_indicator_year['_joinData']['id']]);
                                    echo '<div class="large-4 columns">';
                                    echo '<div class="small-3 columns">';
                                    echo '<label for="right-label" class="right">'.$question_indicator_year['year']['description'].'</label>';
                                    echo '</div>';
                                    echo '<div class="small-9 columns">';
                                    echo $this->Form->control($index.'.value', ['value' => $round_question_indicator_year['_joinData']['value'], 'label' => false]);
                                    echo '</div>';
                                    echo '</div>';
                                    $index = $index + 1;
                                endforeach;
                            endforeach;
                            echo '</div>';
                            echo '<div id="chart-target-'.$question_indicator->indicator->description.'"></div>';
                            echo '</div>';
                        endif;
                    endforeach;
                // end target column
                echo '</div>';
                 echo '<div class="large-6 columns">';
                    foreach($question['questions_indicators'] as $question_indicator):

                        if(!$question_indicator->target):
                            // row by indicator

                            echo '<div class="row">';
                            echo '<h4>'.$question_indicator->indicator->description.'</h4>';
                            echo '<div id="chart-target-'.$question_indicator->indicator->description.'"></div>';
                            echo '</div>';
                        endif;

                    endforeach;

                // end target column
                echo '</div>';

                // end question_indicators row
                echo '</div>';


                echo $this->Form->Button(__('Submit'),['class' => ['button tiny right'],'id' => 'btn-submit']);
                echo $this->Form->end();

                echo '</div>';
            echo '</div>';
            endforeach;?>

    </div>
</div>


<script>


    // functions related with chart data
    function parseChartData(data) {
        var keys = Object.keys(data[0]);
        var cols = [];
        var rows = [];
        for (keyIndex = 0, keysLength = keys.length; keyIndex < keysLength; keyIndex ++)
        {
            if(keyIndex > 1)
                cols.push({label : keys[keyIndex], type : 'number', role:'interval'});
            else
                cols.push({label : keys[keyIndex], type : 'number'});
        }

        for (rowIndex = 0, length = data.length; rowIndex < length; rowIndex ++)
        {
            var rowValues = Object.values(data[rowIndex]);
            var values = [];
            for(colIndex = 0, rowLength = rowValues.length; colIndex < rowLength; colIndex ++)
            {
                values.push({v:rowValues[colIndex]});
            }
            rows.push({c:values});
        }

        return {cols:cols,rows:rows};
    }

    function getIndicatorData(study,scenario,indicator) {
        $.ajax({
            type: "GET",
            url: '/charts/getIndicatorData',
            dataType: 'json',
            data: {
                'study' : study,
                'scenario' : scenario,
                'indicator' : indicator},
            success: function (data)
            {
                console.log(data.response);
                var chartData = parseChartData(data.response);
                drawChart(chartData,'chart-'+indicator,indicator,'Physician');
            }
        });

    }

    function drawChart(chart_data,chart_div, chart1_main_title, chart1_vaxis_title) {
        var chart1_data = new google.visualization.DataTable(chart_data);
        var chart1_options = {
            title: chart1_main_title,
            curveType:'function',
            vAxis: {title: chart1_vaxis_title,  titleTextStyle: {color: 'red'}},
            series: [{'color': '#0a0'}],
            intervals: { 'style':'area' }

        };

        var chart1_chart = new google.visualization.LineChart(document.getElementById(chart_div));
        chart1_chart.draw(chart1_data, chart1_options);
    }



    // functions related with user input
    function objectifyForm(formArray)
    {
        var objectified = {};

        for(i = 0,length = formArray.length; i < length; i++)
        {
            objectified[formArray[i]['name']] = formArray[i]['value'];
        }
        return objectified;
    }

    function validate(data)
    {
        $.ajax({
            type: 'POST',
            url: '/hTwentyForty/rounds/validate',
            dataType: 'json',
            data: data,
            success: function (data)
            {
                if(!data.response)
                {
                    $('#div-round-view-content').prepend('<div class="error message", onclick="this.classList.add(\'hidden\')"> Values inserted does not match</div>')
                }else
                {
                    $('#form-round').submit();
                }
            }
        });
    }


    $(document).ready(function()
    {








       // submit function
       $('#btn-submit').click(function(event)
       {
           event.preventDefault();
           validate(objectifyForm($('#form-round').serializeArray()));

       });
    });
// get loaded charts
</script>
