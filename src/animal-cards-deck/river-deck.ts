/** For animal cards in the river */
class RiverDeck {
	public riverStock: LineStock<AnimalCard>

	/**
	 * Init stock.
	 */
	constructor(private game: HarmoniesGame, private cards: Array<AnimalCard>, private playerCount) {
		let stock = new SlotStock<AnimalCard>(this.game.cardsManager, $(`river`), {
			center: true,
			gap: '7px',
			direction: 'row',
			wrap: 'nowrap',
			slotsIds:
				playerCount === 1
					? ['riverSlot1', 'riverSlot2', 'riverSlot3']
					: ['riverSlot1', 'riverSlot2', 'riverSlot3', 'riverSlot4', 'riverSlot5'],
			mapCardToSlot: (card) => `riverSlot${card.location_arg + 1}`
		})
		stock.setSelectionMode('single')
		this.riverStock = stock
		this.setCards(cards)
		stock.onSelectionChange = (selection: AnimalCard[], lastChange: AnimalCard) =>
			dojo.toggleClass('take_card_button', 'disabled', !(selection.length === 1))!
	}

	/**
	 * Set visible AnimalCard cards.
	 */
	public setCards(cards: AnimalCard[]) {
		this.riverStock.addCards(cards, { fromElement: $('upperrightmenu'), originalSide: 'front' })
	}

	public addCard(card: AnimalCard) {
		this.riverStock.addCard(card, { fromElement: $('upperrightmenu'), originalSide: 'front' })
	}

	public removeCard(AnimalCard: AnimalCard) {
		this.riverStock.removeCard(AnimalCard)
	}

	public setSelectionMode(mode: CardSelectionMode) {
		this.riverStock.setSelectionMode(mode)
	}

	public setSelectableCards(cards: AnimalCard[]) {
		this.riverStock.setSelectableCards(cards)
	}

	public getCards() {
		return this.riverStock.getCards()
	}
	public getSelection() {
		return this.riverStock.getSelection()
	}
}
