<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="index_styles.css" charset="UTF-8">
    <link rel="shortcut icon" href="https://img2.cliparto.com/pic/s/219508/3772747-racing-emblem.jpg">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Cars_race_results</title>
</head>

<body>
    <?php
    $json_cars = file_get_contents("data_cars.json");
    $obj_cars = json_decode($json_cars,true);
    $race_results = array();//массив результатов каждого заезда гонщика
    foreach ($obj_cars['data'] as $car)//заполним сначала известную нам информацию о гонщиках
    {
        array_push($race_results,["id" =>$car["id"],"name"=> $car["name"],
                                         "city" => $car["city"],"car" => $car["car"],
                                         "attempts" => array(), "total_score" => 0]);
    }

    $json_laps = file_get_contents("data_attempts.json");
    $obj_laps = json_decode($json_laps,true);
    foreach ($obj_laps['data'] as $lap_points)//посчитаем кол-во попыток и общий результат гонщика
    {
      $id_driver = $lap_points["id"];
      $id_driver--;
      $attempts = &$race_results[$id_driver]["attempts"];//ссылка на массив результатов каждого заезда
      array_push($attempts,$lap_points["result"]);//передача по ссылке
      $race_results[$id_driver]["total_score"]+=  $lap_points["result"];
    }
    ?>
    
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <th id="not_sortable">Place</th>
            <th id="not_sortable">Name</th>
            <th id="not_sortable">City</th>
            <th id="not_sortable">Car</th>
            <?php //вывод кол-ва попыток в шапку таблицы
            for ($i = 1; $i <= count($race_results[0]["attempts"]); $i++)//возьмем кол-во попыток у первого гонщика в списке и условимся, что кол-во попыток у каждого гонщика одинаковы
            {
                echo "<th>Attempt $i -</th>";
            }
            ?>
            <th id='total_score' class="th-selected_sort">Total_score</th>
        </thead>

        <tbody>
            <?php
            $place = 1;//счетик места гонщиков
            foreach ($race_results as $race_result)//выводим данные о результатах гонки для каждого гонщика
            {
                echo "<tr>";

                    echo "<td>$place</td> ";
                    $place++;

                    $name = $race_result["name"];
                    echo "<td>$name</td> ";

                    $city = $race_result["city"];
                    echo "<td>$city</td> ";

                    $car = $race_result["car"];
                    echo "<td>$car</td> ";

                    foreach($race_result["attempts"] as $attempt_points)//загрузим в таблицу информацию о баллах о каждой поппытке гонщика
                    {
                        echo "<td>$attempt_points</td> ";
                    }

                    $total_score = $race_result["total_score"];
                    echo "<td>$total_score</td>";

                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>

<script type="text/javascript">
    $('th:gt(3)').click( // обработчик нажатия на сортируемые столбцы

        function(event){
        $('th').removeClass("th-selected_sort");//удалим знак выбора сортировки у старого столбца
        $(event.target).addClass("th-selected_sort");//установим знак на новый столбец

        var table = $(this).parents('table').eq(0)//берем первую таблицу
        var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))//отсортированная таблица
        this.asc = !this.asc

        var racer_place=0;//место гонщика
        if (this.asc)
        {
            rows = rows.reverse()
            racer_place = 1;
        }
        else
        {
            racer_place = rows.length;
        }

        for (var i = 0; i < rows.length; i++)
        {
            rows[i].childNodes[0].textContent = racer_place;
            racer_place += this.asc?1:-1;
            table.append(rows[i])
        }
    })
    function comparer(index) {
        return function(a, b) {
            var valA = Get_cell_value(a, index), valB = Get_cell_value(b, index)
            return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
        }
    }
    function Get_cell_value(row, index) {
        return $(row).children('td').eq(index).text()
    }

    //Добавим вызов сортировки по общему кол-ву баллов за все заезды при первой загрузке страницы
    window.onload = function() {
        $('#total_score').trigger('click')
    }
</script>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<!--<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>-->
<!--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>-->
</html>