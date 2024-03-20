/** For animal cards in the river */
class RiverDeck {
    public riverStock: LineStock<AnimalCard>;

    /**
     * Init stock.
     */
    constructor(private game: HarmoniesGame, private cards:Array<AnimalCard>) {
        let stock = new SlotStock<AnimalCard>(this.game.cardsManager, $(`river`), {
			center: true,
			gap: '7px',
			direction: 'row',
			wrap: 'wrap',
			slotsIds: ['riverSlot1', 'riverSlot2', 'riverSlot3', 'riverSlot4', 'riverSlot5'],
			mapCardToSlot: (card) => `riverSlot${card.location_arg + 1}`
		})
        stock.setSelectionMode("single");
        this.riverStock = stock;
        this.setCards(cards)
        stock.onCardClick = (card: AnimalCard) => this.game.takeCard(card);
    }

    /**
     * Set visible AnimalCard cards.
     */
    public setCards(cards: AnimalCard[]) {
        this.riverStock.addCards(cards, { fromElement: $("upperrightmenu"), originalSide: "front" });
    }

    public addCard(card: AnimalCard) {
        this.riverStock.addCard(card, { fromElement: $("upperrightmenu"), originalSide: "front" });
    }

    public removeCard(AnimalCard: AnimalCard) {
        this.riverStock.removeCard(AnimalCard);
    }

    public setSelectionMode(mode: CardSelectionMode) {
        this.riverStock.setSelectionMode(mode);
    }
}
