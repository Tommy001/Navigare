<div class="panel-transparent">
    <div class="jumbotron">
        <div class="container">
            <h1>Navigare Necesse Est</h1>
            <p>Den här webbplatsen är till för dig som älskar båtar. </p>
            <p>Som medlem kan du ställa frågor och svara på andra användares frågor.</p>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class='panel panel-default panel-transparent'>
                <div class='panel-heading'>
                    <h2>Senaste frågorna</h2>
                </div>
                <div class='panel-body'>
                <?=$latest_questions?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class='panel panel-default panel-transparent'>
                <div class='panel-heading'>
                    <h2>Populäraste ämnena</h2>
                </div>     
                <div class='panel-body'>
               <?=$popular_tags?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class='panel panel-default panel-transparent'>
                <div class='panel-heading'>
                    <h2>Aktivaste användarna</h2>
                </div>
                <div class='panel-body'>
                <?=$active_users?>
                </div>
            </div>
        </div>
    </div>
</div> 