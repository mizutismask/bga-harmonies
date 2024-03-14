// <reference path="../card-manager.ts"/>
class CardsManager extends CardManager<AnimalCard> {
	constructor(public game: HarmoniesGame) {
		super(game, {
			animationManager: game.animationManager,
			getId: (card) => `card_${card.id}`,
			setupDiv: (card: AnimalCard, div: HTMLElement) => {
				div.classList.add('harmonies-card')
				div.dataset.cardId = '' + card.id
				div.dataset.cardType = '' + card.type
				div.style.width = '150px'
				div.style.height = '258px'
				div.style.position = 'relative'
			},
			setupFrontDiv: (card: AnimalCard, div: HTMLElement) => {
				this.setFrontBackground(div as HTMLDivElement, card.type_arg)
				//this.setDivAsCard(div as HTMLDivElement, card.type);
				div.id = `${super.getId(card)}-front`

				//add help
				const helpId = `${super.getId(card)}-front-info`
				if (!$(helpId)) {
					const info: HTMLDivElement = document.createElement('div')
					info.id = helpId
					info.innerText = '?'
					info.classList.add('css-icon', 'card-info')
					div.appendChild(info)
					const cardTypeId = card.type * 100 + card.type_arg
					;(this.game as any).addTooltipHtml(info.id, this.getTooltip(card, cardTypeId))
				}

				//adds score locations
				const scoreId = `${super.getId(card)}-front-score`
				if (!$(scoreId)) {
					const container: HTMLDivElement = document.createElement('div')
					container.id = scoreId
					container.classList.add('points-location-wrapper')
					div.appendChild(container)

					card.pointLocations.forEach((pointLoc, i) => {
						const loc: HTMLDivElement = document.createElement('div')
						loc.id = `${super.getId(card)}-score-${i}`
						loc.classList.add('points-location')
						container.appendChild(loc)
					})
				}
			},
			setupBackDiv: (card: AnimalCard, div: HTMLElement) => {
				div.style.backgroundImage = `url('${g_gamethemeurl}img/harmonies-card-background.jpg')`
			}
		})
	}

	public getCardName(cardTypeId: number) {
		return 'todo'
	}

	public getTooltip(card: AnimalCard, cardUniqueId: number) {
		let tooltip = `
		<div class="xpd-city-zoom-wrapper">
			<div id="xpd-city-${cardUniqueId}-zoom" class="xpd-city-zoom" style="${getBackgroundInlineStyleForHarmoniesCard(
			card
		)}"></div>
			<div class="xpd-city-zoom-desc-wrapper">
				<div class="xpd-city">${dojo.string.substitute(_('${to}'), {
					to: 'replace'
				})}</div>
			</div>
		</div>`
		return tooltip
	}

	private setFrontBackground(cardDiv: HTMLDivElement, cardType: number) {
		const destinationsUrl = `${g_gamethemeurl}img/animalCards.jpg`
		cardDiv.style.backgroundImage = `url('${destinationsUrl}')`
		const imagePosition = cardType - 1
		const row = Math.floor(imagePosition / IMAGE_ITEMS_PER_ROW)
		const xBackgroundPercent = (imagePosition - row * IMAGE_ITEMS_PER_ROW) * 100
		const yBackgroundPercent = row * 100
		cardDiv.style.backgroundPositionX = `-${xBackgroundPercent}%`
		cardDiv.style.backgroundPositionY = `-${yBackgroundPercent}%`
		cardDiv.style.backgroundSize = `1000%`
	}
}
