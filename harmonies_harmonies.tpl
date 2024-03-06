{OVERALL_GAME_HEADER}

<audio id="audiosrc_sound1" src="{GAMETHEMEURL}img/sound1.mp3" preload="none"></audio>
<audio id="audiosrc_o_sound1" src="{GAMETHEMEURL}img/sound1.ogg" preload="none"></audio>

<div id="score">
    <div id="table-wrapper">
        <table>
            <thead>
                <tr id="scoretr"></tr>
            </thead>
            <tbody id="score-table-body">
            </tbody>
        </table>
    </div>
</div>

<div id="map-zoom-wrapper">
    <div id="resized">
        <div id="main-line">
            <div id="map-and-borders">
                <div id="map-zoom" class="disable-scrollbars">
                    <div id="map"></div>
                </div>
                <div id="zoom-button"></div>
            </div>
        </div>
    </div>
</div>

<div id="player-tables">
</div>

<div id="board">
  <div id="grid-container">
  <ul class="hex-grid-container">
    <!-- BEGIN cell -->
      <li class="hex-grid-item" id="cell-container-{I}-{J}">
        <div class="hex-grid-content" id="cell-{I}-{J}"></div>
      </li>
    <!-- END cell -->
  </ul>
  </div>
</div>
{OVERALL_GAME_FOOTER}
