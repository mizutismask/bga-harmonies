/**
 * Player table.
 */
class PlayerTable {
	private handStock: PlayerBoardDeck

	constructor(
		private game: HarmoniesGame,
		player: HarmoniesPlayer,
		hexes: Array<Coordinates>,
		cards: Array<AnimalCard>,
		tokensOnBoard: { [hexId: string]: Array<ColoredToken> }
	) {
		const isMyTable = player.id === game.getPlayerId().toString()
		const ownClass = isMyTable ? 'own' : ''
		let html = `
			<a id="anchor-player-${player.id}"></a>
            <div id="player-table-${player.id}" class="player-order${player.playerNo} player-table ${ownClass}">
				<div id="board-${player.id}" class="hrm-player-board">
					<div id="grid-container-${player.id}">
						<ul id="hex-grid-container-${player.id}" class="hex-grid-container"></ul>
					</div>
				</div>
				<span class="player-name">${player.name}</span>
            </div>
        `
		dojo.place(html, 'player-tables')

		hexes.forEach((hex) => {
			const cellContainerName = `${player.id}-cell-container-${hex.col}-${hex.row}`

			const cellName = `${player.id}_cell_${hex.col}_${hex.row}`
			let html = `
			<li class="hex-grid-item" id="${cellContainerName}">
				<div class="hex-grid-content" id="${cellName}"></div>
		  	</li>
        `
			dojo.place(html, `hex-grid-container-${player.id}`)

			const cellT = $(cellContainerName)
			cellT.style.gridRow = 2 * hex.row + (hex.col % 2 == 0 ? 1 : 2) + ' / span 2'
			cellT.style.gridColumn = 3 * hex.col + 1 + ' / span 4'
		})

		if (isMyTable) {
			dojo.connect($(`grid-container-${player.id}`), 'click', (evt) => {
				if (evt.target.id.startsWith(`${player.id}_cell_`)) {
					this.game.placeToken(evt.target.id)
				} else {
					evt.preventDefault()
					evt.stopPropagation()
				}
			})
		}
		Object.keys(tokensOnBoard).forEach((cell) => {
			tokensOnBoard[cell].forEach((token) => this.createTokenOnBoard(token))
		})

		const handHtml = `
			<div id="hand-${player.id}" class="hrm-player-hand"></div>
        `
		dojo.place(handHtml, `player-table-${player.id}`, 'first')
		this.initHand(player, cards)
	}

	private initHand(player: HarmoniesPlayer, cards: Array<AnimalCard>) {
		this.handStock = new PlayerBoardDeck(this.game, player, cards)
	}

	public addCard(card: AnimalCard) {
		this.handStock.addCard(card)
	}

	/**
	 * Creates a new div inside an hex
	 * @param args
	 */
	public createTokenOnBoard(token: ColoredToken) {
		let html = `
			<div class="colored-token color-${token.type_arg}"></div>
        `
		dojo.place(html, token.location)
	}
}
