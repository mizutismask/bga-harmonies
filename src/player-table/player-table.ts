/**
 * Player table.
 */
class PlayerTable {
	private handStock: PlayerBoardDeck
	private doneStock: LineStock<AnimalCard>

	constructor(
		private game: HarmoniesGame,
		player: HarmoniesPlayer,
		boardSize: { width: number; height: number },
		hexes: Array<Coordinates>,
		cards: Array<AnimalCard>,
		tokensOnBoard: { [hexId: string]: Array<ColoredToken> },
		animalCubesOnBoard: { [hexId: string]: Array<AnimalCube> },
		doneAnimalCards: Array<AnimalCard>
	) {
		const isMyTable = player.id === game.getPlayerId().toString()
		const ownClass = isMyTable ? 'own' : ''
		let html = `
			<a id="anchor-player-${player.id}"></a>
            <div id="player-table-${player.id}" class="player-order${player.playerNo} player-table ${ownClass}">
				<div id="board-${player.id}" class="hrm-player-board">
					<div id="grid-container-${player.id}">
						<div id="hex-grid-container-${player.id}" class="hex-grid-container"></div>
					</div>
				</div>
				<span class="player-name" style="color:#${player.color}">${player.name}</span>
            </div>
        `
		dojo.place(html, 'player-tables')

		for (let row = 0; row < boardSize.height; row++) {
			for (let col = 0; col < boardSize.width; col++) {
				const cellName = `${player.id}_cell_${col}_${row}`
				let html = `
						<div class="hex invisible" id="${cellName}">
						</div>
					`
				dojo.place(html, `hex-grid-container-${player.id}`)
			}
		}
		hexes.forEach((h) => $(`${player.id}_cell_${h.col}_${h.row}`).classList.remove('invisible'))

		if (isMyTable) {
			dojo.connect($(`grid-container-${player.id}`), 'click', (evt) => {
				log(
					'container click on :',
					evt.target.id,
					'starts with',
					`${player.id}_cell_`,
					evt.target.id.startsWith(`${player.id}_cell_`)
				)
				if (
					!evt.target.id.startsWith(`${player.id}_cell_container`) &&
					evt.target.id.startsWith(`${player.id}_cell_`)
				) {
					this.game.onHexClick(evt.target.id)
				} else {
					evt.preventDefault()
					evt.stopPropagation()
				}
			})
		}

		this.initDoneStock(player, doneAnimalCards)

		Object.keys(tokensOnBoard).forEach((cell) => {
			tokensOnBoard[cell].forEach((token) => this.createTokenOnBoard(token))
		})

		if (animalCubesOnBoard) {
			Object.keys(animalCubesOnBoard).forEach((cell) => {
				animalCubesOnBoard[cell].forEach((cube) => this.createCubeOnBoard(cube))
			})
		}

		const handHtml = `
			<div id="hand-${player.id}" class="hrm-player-hand"></div>
        `
		dojo.place(handHtml, `player-table-${player.id}`, 'first')
		this.initHand(player, cards)
	}

	private initDoneStock(player: HarmoniesPlayer, doneAnimalCards: Array<AnimalCard>) {
		const doneHtml = `
			<div id="done-${player.id}" class="hrm-player-done"></div>
		`
		dojo.place(doneHtml, `player-table-${player.id}`)
		this.doneStock = new LineStock<AnimalCard>(this.game.cardsManager, $('done-' + player.id), {
			center: true,
			gap: '7px',
			direction: 'row',
			wrap: 'nowrap'
		})
		this.doneStock.setSelectionMode('none')
		this.doneStock.addCards(doneAnimalCards)
	}

	private initHand(player: HarmoniesPlayer, cards: Array<AnimalCard>) {
		this.handStock = new PlayerBoardDeck(this.game, player, cards)
	}

	public addCard(card: AnimalCard) {
		this.handStock.addCard(card)
	}

	public addDoneCard(card: AnimalCard) {
		this.doneStock.addCard(card)
	}

	public selectCardFromId(cardId: number) {
		this.handStock.selectCardFromId(cardId)
	}

	/**
	 * Creates a new div inside an hex
	 * @param args
	 */
	public createTokenOnBoard(token: ColoredToken) {
		let html = `
			<div class="colored-token color-${token.type_arg} level-${token.location_arg}"></div>
        `
		dojo.place(html, token.location)
	}

	public createCubeOnBoard(cube: AnimalCube) {
		log('createCubeOnBoard', cube)
		let html = `
			<div class="animal-cube cube"></div>
        `
		dojo.place(html, cube.location, 'first')
	}

	public setSelectionMode(mode: CardSelectionMode) {
		this.handStock.setSelectionMode(mode)
	}

	public unselectAll() {
		this.handStock.unselectAll()
	}

	public getAnimalCardSelection() {
		return this.handStock.getSelection()
	}
}
