/**
 * Base class for animations.
 */
abstract class HarmoniesAnimation {
	protected zoom: number;

	constructor(protected game: HarmoniesGame) {
		this.zoom = this.game.getZoom();
	}

	public abstract animate(): Promise<HarmoniesAnimation>;
}
