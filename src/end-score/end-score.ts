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
                        <span>${player.name.substring(0, Math.min(12, player.name.length))}</span>
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
					`<div id="score-card-${index}-${player.id}" class="${
						index == this.game.isSpiritCardsOn() && index == 1 ? 'spirit-score' : ''
					} score-number"></div>`,
					`cards-container-${player.id}`
				)
			}
			//totals
			;[1, 2, 3].forEach((totalType) => {
				dojo.place(
					`<div id="score-total-${totalType}-${player.id}" class="score-number"></div>`,
					`totals-container-${player.id}`
				)
			})
		})
		this.updateScores(players)
	}

	public updateScores(players: HarmoniesPlayer[]) {
		players.forEach((p) => {
			if (p.scores) {
				Object.entries(p.scores).forEach(([type, delta]) => {
					this.updateScore(parseInt(p.id), type, delta, false)
				})
			}
		})
	}

	private preventMinusZero(score: number) {
		if (score === 0) {
			return '0'
		}
		return '-' + score.toString()
	}

	public updateScore(playerId: number, scoreType: string, score: number, animate: boolean = true) {
		const elt = dojo.byId(scoreType)
		if (!elt) {
			console.error('updateScore : this element can not be displayed', scoreType)
		} else {
			elt.innerHTML = score.toString()
			if (animate) {
				dojo.addClass(scoreType, 'animatedScore')
			}
		}
	}

	/**
	 * Add trophee icon to top score player(s)
	 */
	public highlightWinnerScore(playerId: number | string) {
		document.getElementById(`score${playerId}`).classList.add('highlight')
		//document.getElementById(`score-winner-${playerId}`).classList.add('fa', 'fa-trophy', 'fa-lg')
	}
}
