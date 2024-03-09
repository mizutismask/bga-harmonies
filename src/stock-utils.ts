const CARD_WIDTH = 300; //also change in scss
const CARD_HEIGHT = 516;

function getBackgroundInlineStyleForHarmoniesCard(destination: AnimalCard) {
	let file
	switch (destination.type) {
		case 1:
			file = 'animalCards.jpg'
			break
	}

	const imagePosition = destination.type_arg - 1
	const row = Math.floor(imagePosition / IMAGE_ITEMS_PER_ROW)
	const xBackgroundPercent = (imagePosition - row * IMAGE_ITEMS_PER_ROW) * 100
	const yBackgroundPercent = row * 100
	return `background-image: url('${g_gamethemeurl}img/${file}'); background-position: -${xBackgroundPercent}% -${yBackgroundPercent}%; background-size:1000%;`
}
