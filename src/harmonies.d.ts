/**
 * Your game interfaces
 */
declare const define
declare const ebg
declare const $
declare const dojo: Dojo
declare const _
declare const g_gamethemeurl

// remove this if you don't use cards. If you do, make sure the types are correct . By default, some number are send as string, I suggest to cast to right type in PHP.
interface Card {
	id: number
	location: string
	location_arg: number
	type: number
	type_arg: number
}
interface AnimalCard extends Card {
	pointLocations: Array<number>
	isSpirit: boolean
}

interface ColoredToken extends Card {}
interface AnimalCube extends Card {}

interface HarmoniesPlayer extends Player {
	playerNo: number
	boardAnimalCards: Array<AnimalCard>
	tokensOnBoard: { [hexId: string]: Array<ColoredToken> }
	animalCubesOnBoard: { [hexId: string]: Array<AnimalCube> }
	doneAnimalCards: Array<AnimalCard>
	scores?: { [scoreType: string]: number }
	emptyHexes: number
}

type Coordinates = { col: number; row: number }

interface HarmoniesGamedatas {
	current_player_id: string
	decision: { decision_type: string }
	game_result_neutralized: string
	gamestate: Gamestate
	gamestates: { [gamestateId: number]: Gamestate }
	neutralized_player_id: string
	notifications: { last_packet_id: string; move_nbr: string }
	playerorder: (string | number)[]
	playerOrderWorkingWithSpectators: number[] //starting with current player
	players: { [playerId: number]: HarmoniesPlayer }
	tablespeed: string
	lastTurn: boolean
	turnOrderClockwise: boolean
	// counters
	scores?: Array<NotifScoreArgs>
	winners: number[]
	version: string
	// Add here variables you set up in getAllDatas
	boardSide: string
	boardSize: { width: number; height: number }
	hexes: Array<Coordinates>
	river: Array<AnimalCard>
	cubesOnAnimalCards: Array<AnimalCube>
	tokensOnCentralBoard: { [hole: number]: Array<ColoredToken> }
	expansion: number
	spiritsCards: AnimalCard[]
	remainingTokens: number
}

interface HarmoniesGame extends Game {
	clientActionData: ClientActionData
	cardsManager: CardsManager
	animationManager: AnimationManager
	getCurrentPlayer(): HarmoniesPlayer
	getPlayerId(): number
	getPlayerScore(playerId: number): number
	setTooltip(id: string, html: string): void
	setTooltipToClass(className: string, html: string): void
	takeCard(card: AnimalCard): void
	onHexClick(hexId: string): void
	isSpiritCardsOn(): unknown
	getNextTokenId(): string
	takeAction(action: string, data?: any): void
	resetClientActionData(): void
	isConfirmOnlyOnPlacingTokensOn(): boolean
	toggleActionButtonAbility(buttonId: string, enable: boolean): void
	getHelpOnCardConfig(): number
	slide(mobileElt, targetElt, options)
}

interface EnteringChooseActionArgs {
	canResetTurn: boolean
	canPass: boolean
	canTakeTokens: boolean
	canPlaceToken: boolean
	canTakeAnimalCard: boolean
	canPlaceAnimalCube: boolean
	canChooseSpirit: boolean
	tokensOnCentralBoard: any
	tokensToPlace: Array<ColoredToken>
	placeAnimalCubeArgs: { [cardId: number]: Array<string> }
	possibleHexesByToken: { [cardId: number]: Array<string> }
}

interface NotifPointsArgs {
	playerId: number
	points: number
	delta: number
	scoreType: string
}

interface NotifScoreArgs {
	playerId: number
	score: number
	scoreType: string
}

interface NotifCounter {
	counterName: string
	counterValue: number
	playerId?: number
}

interface NotifWinnerArgs {
	playerId: number
}

interface NotifScorePointArgs {
	playerId: number
	points: number
}

interface NotifMaterialMove {
	type: 'CARD' | 'TOKEN' | 'FIRST_PLAYER_TOKEN' | 'CUBE'
	from: 'HAND' | 'DECK' | 'STOCK' | 'RIVER' | 'SPIRITS' | 'HOLE'
	to: 'HAND' | 'DECK' | 'STOCK' | 'CARD' | 'HEX' | 'HOLE' | 'DONE' | 'SPIRITS' | 'RIVER' | 'DISCARD'
	fromArg: number | string
	toArg: number | string
	//context?: number | string | boolean
	material: Array<any | string> //elements (cards for exemple), or tokenIds
}

interface NotifHoleEmptied {
	hole: number
}

interface ClientActionData {
	tokenToPlace: ColoredToken
}

interface ActionCallback {
	valid: boolean
	data: any
}
