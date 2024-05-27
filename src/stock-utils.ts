const CARD_WIDTH = 150 //also change in scss
const CARD_HEIGHT = 258

function getBackgroundInlineStyleForAnimalCard(card: AnimalCard) {
	let file
	switch (card.type) {
		case 1:
			file = 'animalCards.jpg'
			break
	}

	const imagePosition = card.type_arg - 1
	const row = Math.floor(imagePosition / IMAGE_ITEMS_PER_ROW)
	const xBackgroundPercent = (imagePosition - row * IMAGE_ITEMS_PER_ROW) * 100
	const yBackgroundPercent = row * 100
	return `background-image: url('${g_gamethemeurl}img/${file}'); background-position: -${xBackgroundPercent}% -${yBackgroundPercent}%; background-size:1000%;`
}
