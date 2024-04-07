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
		let tooltip = ''
		if (card.isSpirit) {
			const desc = this.getSpiritDescription(card)
			tooltip = `
			<div class="xpd-city-zoom-wrapper">
				<div class="xpd-city-zoom-desc-wrapper">
					<div class="xpd-city">${desc}</div>
				</div>
			</div>`
		} else {
			tooltip = `
			<div class="xpd-city-zoom-wrapper">
				<div class="xpd-city-zoom-desc-wrapper">
					<div class="xpd-city">${dojo.string.substitute(
						_('Gain those points if you put a cube on this exact pattern several times: ${points}'),
						{
							points: card.pointLocations.reverse().join(', ')
						}
					)}</div>
				</div>
			</div>`
		}
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
		cardDiv.style.backgroundSize = `${IMAGE_ITEMS_PER_ROW * 100}%`
	}

	private getSpiritDescription(card: AnimalCard) {
		switch (card.type_arg) {
			case 33:
				return _('Gain 10 points for each group of 3 fields tokens or more, 2 otherwise')
			case 34:
				return _('Gain 5 points for each group of 1 field token or more ')
			case 35:
				return _('Gain 4 points for each tree of height 2 or 3')
			case 36:
				return _('Gain 3 points for each tree of height 1 or 2, 1 otherwise')
			case 37:
				return _('Gain 4 points for each group of 1 building or more')
			case 38:
				return _('Gain 4 points for each group of 2 buildings or more')
			case 39:
				return _('Gain 4 points for each moutain of height 2 or 3')
			case 40:
				return _('Gain 3 points for each moutain of height 1 or 2, 1 otherwise')
			case 41:
				return _('Gain 7 points for each group of 2 water tokens or more')
			case 42:
				return _('Gain 2 points for each water token')

			default:
				console.error('no tooltip available for spirit ' + card.type_arg)
				break
		}
	}
}
