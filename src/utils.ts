function addTemporaryClass(element: HTMLElement | string, className: string, removalDelay: number) {
	dojo.addClass(element, className)
	setTimeout(() => dojo.removeClass(element, className), removalDelay)
}

function removeClass(className: string, rootNode?: HTMLElement | Document): void {
	if (!rootNode) rootNode = document
	else rootNode = rootNode as HTMLElement
	rootNode.querySelectorAll('.' + className).forEach((item) => item.classList.remove(className))
}

/*
 * Detect if spectator or replay
 */
function isReadOnly() {
	return this.isSpectator || typeof (this as any).g_replayFrom != 'undefined' || (this as any).g_archive_mode
}

function isValueInRange(value: number, minValue: number, maxValue: number): boolean {
	return value >= minValue && value <= maxValue
}

function getCubeClasses(cube: AnimalCube) {
	return `animal-cube ${cube.type_arg === 2 ? 'cubespirit' : 'cube'}`
}

function getCellName(playerId, hex) {
	return getCellNameFromCoords(playerId, hex['col'], hex['row'])
}

function getCellNameFromCoords(playerId, col: number, row: number) {
	return `cell_${playerId}_${col}_${row}`
}

function replaceStarScoreIcon(newClass: string) {
	dojo.query('.fa-star')
		.removeClass('fa fa-star')
		.addClass(newClass)
		.style({ 'vertical-align': 'middle', 'display': 'inline-block' })
}
