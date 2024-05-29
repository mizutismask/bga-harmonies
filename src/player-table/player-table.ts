/**
 * Player table.
 */
class PlayerTable {
	private handStock: PlayerBoardDeck
	private doneStock: LineStock<AnimalCard>
	private spiritsStock: LineStock<AnimalCard>
	private tempHandlers: any[] = []

	constructor(
		private game: HarmoniesGame,
		private player: HarmoniesPlayer,
		boardSize: { width: number; height: number },
		hexes: Array<Coordinates>,
		cards: Array<AnimalCard>,
		tokensOnBoard: { [hexId: string]: Array<ColoredToken> },
		animalCubesOnBoard: { [hexId: string]: Array<AnimalCube> },
		doneAnimalCards: Array<AnimalCard>,
		spiritCards: Array<AnimalCard>
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
				const cellName = getCellNameFromCoords(player.id, col, row)
				let html = `
						<div class="hex invisible" id="${cellName}">
						</div>
					`
				dojo.place(html, `hex-grid-container-${player.id}`)
			}
		}
		hexes.forEach((h) => $(getCellNameFromCoords(player.id, h.col, h.row)).classList.remove('invisible'))
		if (isMyTable) {
			dojo.connect($(`grid-container-${player.id}`), 'click', (evt) => {
				log(
					'container click on :',
					evt.target.id,
					'starts with',
					`${player.id}_cell_`,
					evt.target.id.startsWith(`${player.id}_cell_`)
				)
				this.game.onHexClick(evt.target.id)
			})
		}

		this.initDoneStock(player, doneAnimalCards)

		Object.keys(tokensOnBoard).forEach((cell) => {
			tokensOnBoard[cell].forEach((token) => this.createTokenOnBoard(token))
		})

		if (animalCubesOnBoard) {
			Object.keys(animalCubesOnBoard).forEach((cell) => {
				animalCubesOnBoard[cell].forEach((cube) => this.createCubeOnBoard(cube, undefined, false))
			})
		}

		const handHtml = `
			<div id="hand-zone-${player.id}" class="hand-zone">
				<div class="border"></div>
				<div id="hand-${player.id}" class="hrm-player-hand"></div>
			</div>
        `
		dojo.place(handHtml, `player-table-${player.id}`, 'first')
		this.initHand(player, cards)

		const takenTokensHtml = `
			<div id="taken-tokens-zone-${player.id}" class="taken-tokens-zone">
				<div class="border"></div>
				<div id="taken-tokens-${player.id}" class="taken-tokens"></div>
			</div>
        `
		dojo.place(takenTokensHtml, `board-${player.id}`, 'after')

		if (isMyTable) {
			if (this.game.isSpiritCardsOn()) {
				this.initSpiritsStock(player, spiritCards)
			}
		}
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

	private initSpiritsStock(player: HarmoniesPlayer, spiritsCards: Array<AnimalCard>) {
		const container = `
			<div id="spirits-zone-${player.id}" class="hrm-player-spirits">
				<div class="border"></div>
				<div id="spirits-${player.id}"></div>
			</div>
		`
		dojo.place(container, `player-table-${player.id}`, 'first')
		this.spiritsStock = new LineStock<AnimalCard>(this.game.cardsManager, $('spirits-' + player.id), {
			center: true,
			gap: '7px',
			direction: 'row',
			wrap: 'nowrap'
		})
		this.spiritsStock.setSelectionMode('single')
		this.spiritsStock.addCards(spiritsCards)

		this.spiritsStock.onSelectionChange = (selection: AnimalCard[], lastChange: AnimalCard) => {
			this.game.toggleActionButtonAbility('take_spirit_button', selection.length === 1)
		}
	}

	private initHand(player: HarmoniesPlayer, cards: Array<AnimalCard>) {
		this.handStock = new PlayerBoardDeck(this.game, player, cards)

		this.handStock.boardDeck.onSelectionChange = (selection: AnimalCard[], lastChange: AnimalCard) => {
			this.game.toggleActionButtonAbility(
				'place_cube_confirm_button',
				selection.length === 1 && document.querySelector('.hex.selected-element') != undefined
			)
		}
	}

	public addCard(card: AnimalCard) {
		this.handStock.addCard(card)
	}

	public addSpiritCard(card: AnimalCard) {
		this.spiritsStock.addCard(card)
	}

	public removeAllSpiritsCards(): void {
		this.spiritsStock.removeAll()
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
	public createTokenOnBoard(token: ColoredToken, animate: boolean = false) {
		const divId = `token-${token.id}`
		const div = $(divId)
		if (animate && div) {
			div.classList.remove("selected", "taken-token", "level-1", "level-2", "level-3")
			div.classList.add(`level-${token.location_arg}`)
			
			//shows move only coming inside the hex because of hex overflow
			/*this.game.animationManager.attachWithAnimation(
				new BgaSlideAnimation({
					element: div
				}),
				$(token.location)
			)*/
			this.game.slide(div.id, token.location, {phantom: false });
		} else {
			const tokenId = this.game.getNextTokenId()
			let html = `
			<div id="${tokenId}" class="colored-token color-${token.type_arg} level-${token.location_arg}"></div>
        `
			this.createElementOnBoard(html, tokenId, token.location, '', animate)
		}
	}

	public disconnectTempHandlers() {
		this.tempHandlers.forEach((h) => dojo.disconnect(h))
		this.tempHandlers = []
	}

	public createTokensOnTakenTokensZone(
		tokens: ColoredToken[],
		possibleHexesByToken: { [cardId: number]: Array<string> },
		animate: boolean = false
	) {
		this.disconnectTempHandlers()
		dojo.empty(`taken-tokens-${this.player.id}`)
		tokens.forEach((token, i) => {
			const tokenId = 'token-' + token.id
			let html = `
			<div id="${tokenId}" class="colored-token color-${token.type_arg} taken-token level-${token.location_arg}"></div>
        `
			this.createElementOnBoard(html, tokenId, `taken-tokens-${this.player.id}`, '', animate)

			this.tempHandlers.push(
				dojo.connect($(`${tokenId}`), 'onclick', (evt) => {
					const buttonId = evt.target.id
					log('click on', buttonId)
					this.game.resetClientActionData()

					//allow only 1 token to be selected
					const selected = $(buttonId).classList.toggle('selected')
					if (selected) {
						this.game.clientActionData.tokenToPlace = token
						dojo.query(`#taken-tokens-${this.player.id} .taken-token:not(#${buttonId})`).toggleClass(
							'selected',
							false
						)
					}

					//show possible places for colored tokens
					removeClass('selectable-element')
					if (possibleHexesByToken) {
						possibleHexesByToken[getPart(tokenId, -1)].forEach((h) =>
							$(h).classList.add('selectable-element')
						)
					}
				})
			)
			log(this.tempHandlers)
		})
	}

	public createCubeOnBoard(cube: AnimalCube, fromCardId: string, animate: boolean = false) {
		const tokenId = this.game.getNextTokenId()
		const pileHeight = dojo.query(`#${cube.location} .colored-token`).length
		let html = `
			<div class="animal-cube ${getCubeClasses(cube)} pile-height-${pileHeight}"></div>
        `
		//console.log("location", cube.location, "frorm",fromCardId);
		this.createElementOnBoard(html, tokenId, cube.location, 'first', false)
	}

	private createElementOnBoard(
		html: string,
		htmlId: string,
		location: string,
		action: string,
		animate: boolean = false,
		animateFrom: string = undefined
	) {
		const destination = location
		let creationLocation = animateFrom ?? location
		if (animate && !animateFrom) {
			creationLocation = `overall_player_board_${this.player.id}`
		}
		dojo.place(html, creationLocation, action)

		if (animate) {
			//shows move only coming inside the hex because of hex overflow
			/*this.game.animationManager.attachWithAnimation(
				new BgaSlideAnimation({
					element: $(htmlId)
				}),
				$(destination)
			)*/
			this.game.slide(htmlId, destination, {phantom: false });
		}
	}

	public setSelectionMode(mode: CardSelectionMode) {
		this.handStock.setSelectionMode(mode)
	}

	public setSpiritSelectionMode(mode: CardSelectionMode) {
		this.spiritsStock.setSelectionMode(mode)
	}

	public unselectAll() {
		this.handStock.unselectAll()
	}

	public getAnimalCardSelection() {
		return this.handStock.getSelection()
	}
	public getSpiritCardSelection() {
		return this.spiritsStock.getSelection()
	}
}
