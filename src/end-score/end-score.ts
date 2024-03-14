declare const playSound

/**
 * End score board.
 * No notifications.
 */
class ScoreBoard {
	constructor(private game: HarmoniesGame, private players: HarmoniesPlayer[]) {
		players.forEach((player) => {
			const playerId = Number(player.id)

			const playerContainer = `score${player.id}`
			dojo.place(
				`<div id="${playerContainer}" class="score-player-container">
                    <div id="score-name-${player.id}" class="player-name" style="color: #${player.color}">
                        <span>${player.name}</span>
                    </div>
                    <div id="land-container-${player.id}" class="land-container"></div>
                    <div id="cards-container-${player.id}" class="cards-container"></div>
                    <div id="totals-container-${player.id}" class="totals-container"></div>
                </div`,
				'score-wrapper'
			)

                //first column
			;[4, 2, 5, 6, 1].forEach((landType) => {
				dojo.place(
					`<div id="score-land-${landType}-${player.id}" class="score-number"></div>`,
					`land-container-${player.id}`
				)
            })
            //second column
            for (let index = 1; index <= 8; index++) {
                dojo.place(
					`<div id="score-card-${index}-${player.id}" class="score-number"></div>`,
					`cards-container-${player.id}`
				)
            }
            //totals
            [1,2,3].forEach((totalType) => {
				dojo.place(
					`<div id="score-total-${totalType}-${player.id}" class="score-number"></div>`,
					`totals-container-${player.id}`
				)
            })

			/*dojo.place(
				`<tr id="score${player.id}">
                    <td id="score-name-${player.id}" class="player-name" style="color: #${player.color}">
                        <span id="score-winner-${player.id}"/> <span>${player.name}</span>
                    </td>
                    <td id="round-1-goal-2-${player.id}" class="score-number">${0}</td>
                    <td id="round-1-goal-1-${player.id}" class="score-number">${0}</td>
                    <td id="total-round-1-${player.id}" class="score-number total">0</td>

                    <td id="round-2-goal-1-${player.id}" class="score-number">${0}</td>
                    <td id="round-2-goal-4-${player.id}" class="score-number">${0}</td>
                    <td id="total-round-2-${player.id}" class="score-number total">0</td>

                    <td id="round-3-goal-2-${player.id}" class="score-number">${0}</td>
                    <td id="round-3-goal-3-${player.id}" class="score-number">${0}</td>
                    <td id="total-round-3-${player.id}" class="score-number total">0</td>

                    <td id="round-4-goal-1-${player.id}" class="score-number">${0}</td>
                    <td id="round-4-goal-4-${player.id}" class="score-number">${0}</td>
                    <td id="round-4-goal-3-${player.id}" class="score-number">${0}</td>
                    <td id="total-round-4-${player.id}" class="score-number total">0</td>

                    <td id="round-5-goal-2-${player.id}" class="score-number">${0}</td>
                    <td id="round-5-goal-3-${player.id}" class="score-number">${0}</td>
                    <td id="round-5-goal-4-${player.id}" class="score-number">${0}</td>
                    <td id="total-round-5-${player.id}" class="score-number total">0</td>
                    
                    <td id="total-${player.id}" class="score-number total">${player.score}</td>
                </tr>`,
                "score-table-body"
            );*/
		})
	}

	public updateScores(players: HarmoniesPlayer[]) {
		/*players.forEach((p) => {
            document.getElementById(`destination-reached${p.id}`).innerHTML = (
                p.completedDestinations.length + p.sharedCompletedDestinationsCount
            ).toString();
            document.getElementById(`revealed-tokens-back${p.id}`).innerHTML = p.revealedTokensBackCount.toString();
            document.getElementById(`destination-unreached${p.id}`).innerHTML = this.preventMinusZero(
                p.uncompletedDestinations?.length
            );
            document.getElementById(`revealed-tokens-left${p.id}`).innerHTML = this.preventMinusZero(
                p.revealedTokensLeftCount
            );
            document.getElementById(`total${p.id}`).innerHTML = p.score.toString();
        });*/
	}

	private preventMinusZero(score: number) {
		if (score === 0) {
			return '0'
		}
		return '-' + score.toString()
	}

	public updateScore(playerId: number, scoreType: string, score: number) {
		const elt = dojo.byId(scoreType)
		elt.innerHTML = score.toString()
		dojo.addClass(scoreType, 'animatedScore')
	}

	/**
	 * Add trophee icon to top score player(s)
	 */
	public highlightWinnerScore(playerId: number | string) {
		document.getElementById(`score${playerId}`).classList.add('highlight')
		//document.getElementById(`score-winner-${playerId}`).classList.add('fa', 'fa-trophy', 'fa-lg')
	}
}
