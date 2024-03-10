/** For animal cards in the river */
class RiverDeck {
    public riverStock: LineStock<AnimalCard>;

    /**
     * Init stock.
     */
    constructor(private game: HarmoniesGame, private cards:Array<AnimalCard>) {
        let stock = new LineStock<AnimalCard>(
            this.game.cardsManager,
            $(`river`),
            {
                center: true,
                gap: "7px",
                direction: "row",
                wrap: "wrap",
            }
        );
        stock.setSelectionMode("single");
        this.riverStock = stock;
        this.setCards(cards)
    }

    /**
     * Set visible AnimalCard cards.
     */
    public setCards(cards: AnimalCard[]) {
        this.riverStock.addCards(cards, { fromElement: $("upperrightmenu"), originalSide: "front" });
    }

    public removeCard(AnimalCard: AnimalCard) {
        this.riverStock.removeCard(AnimalCard);
    }
}
