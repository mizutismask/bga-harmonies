/** For animal cards on the top of each player board */
class PlayerBoardDeck {
	public boardDeck: LineStock<AnimalCard>

	/**
	 * Init stock.
	 */
	constructor(private game: HarmoniesGame, player: HarmoniesPlayer, cards: Array<AnimalCard>) {
		let stock = new SlotStock<AnimalCard>(this.game.cardsManager, $('hand-' + player.id), {
			center: true,
			gap: '7px',
			direction: 'row',
			wrap: 'nowrap',
			slotsIds: ['slot1', 'slot2', 'slot3', 'slot4'],
			mapCardToSlot: (card) => `slot${card.location_arg + 1}`
		})
		stock.setSelectionMode('none')
		this.boardDeck = stock
		this.setCards(cards)
	}

	/**
	 * Set visible AnimalCard cards.
	 */
	public setCards(cards: AnimalCard[]) {
		this.boardDeck.addCards(cards, { fromElement: $('upperrightmenu'), originalSide: 'front' })
	}

	public addCard(card: AnimalCard) {
		this.boardDeck.addCard(card)// { fromElement: $('upperrightmenu'), originalSide: 'front' }
	}

	public removeCard(AnimalCard: AnimalCard) {
		this.boardDeck.removeCard(AnimalCard)
	}
}
