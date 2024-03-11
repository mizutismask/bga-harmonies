/**
 * Player table.
 */
class PlayerTable {
	private handStock: PlayerBoardDeck

	constructor(private game: HarmoniesGame, player: HarmoniesPlayer, hexes: Array<Coordinates>) {
		const isMyTable = player.id === game.getPlayerId().toString()
		const ownClass = isMyTable ? 'own' : ''
		let html = `
			<a id="anchor-player-${player.id}"></a>
            <div id="player-table-${player.id}" class="player-order${player.playerNo} player-table ${ownClass}">
			    <span class="player-name">${player.name}</span>
				<div id="board-${player.id}" class="hrm-player-board">
					<div id="grid-container-${player.id}">
						<ul id="hex-grid-container-${player.id}" class="hex-grid-container"></ul>
					</div>
				</div>
            </div>
        `
		dojo.place(html, 'player-tables')

		hexes.forEach((hex) => {
			const cellName =  `${player.id}-cell-container-${hex.col}-${hex.row}`
			let html = `
			<li class="hex-grid-item" id="${cellName}">
				<div class="hex-grid-content" id="${player.id}-cell-${hex.col}-${hex.row}"></div>
		  	</li>
        `
			dojo.place(html,  `hex-grid-container-${player.id}`)

			const cellT = $(cellName)
			cellT.style.gridRow = 2 * hex.row + (hex.col % 2 == 0 ? 1 : 2) + ' / span 2'
			cellT.style.gridColumn = 3 * hex.col + 1 + ' / span 4'

			/*dojo.connect($('cell-' + i + '-' + j), 'onclick', (evt) => {
				evt.preventDefault();
				evt.stopPropagation();
				this.onClickCell(x, y);
			  });*/
		})

		if (isMyTable) {
			const handHtml = `
			<div id="hand-${player.id}" class="nml-player-hand"></div>
        `
			dojo.place(handHtml, `player-table-${player.id}`, 'first')
			this.initHand(player)
		}
	}

	private initHand(player: HarmoniesPlayer) {
		this.handStock = new PlayerBoardDeck(this.game, player)
	}
}
