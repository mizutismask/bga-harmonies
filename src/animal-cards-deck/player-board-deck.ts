/** For animal cards on the top of each player board */
class PlayerBoardDeck {
    public boardDeck: LineStock<AnimalCard>;

    /**
     * Init stock.
     */
    constructor(private game: HarmoniesGame, player: HarmoniesPlayer,) {
        let stock = new LineStock<AnimalCard>(
            this.game.cardsManager,
            $('hand-' + player.id),
            {
                center: true,
                gap: "7px",
                direction: "column",
                wrap: "nowrap",
            }
        );
        stock.setSelectionMode("single");
        this.boardDeck = stock;
    }

    /**
     * Set visible AnimalCard cards.
     */
    public setCards(destinations: AnimalCard[]) {
        this.boardDeck.addCards(destinations, { fromElement: $("upperrightmenu"), originalSide: "back" });
    }

    public removeCard(AnimalCard: AnimalCard) {
        this.boardDeck.removeCard(AnimalCard);
    }
}
