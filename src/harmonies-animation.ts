/**
 * Base class for animations.
 */
abstract class HarmoniesAnimation {
	protected zoom: number;

	constructor(protected game: HarmoniesGame) {
		this.zoom = 1;
	}

	public abstract animate(): Promise<HarmoniesAnimation>;
}
