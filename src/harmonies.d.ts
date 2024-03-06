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
interface HarmoniesCard extends Card {}

interface HarmoniesPlayer extends Player {
	playerNo: number
}

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
	boardSize: { width: number, height:number }
}

interface HarmoniesGame extends Game {
	cardsManager: CardsManager
	animationManager: AnimationManager
	getZoom(): number
	getCurrentPlayer(): HarmoniesPlayer
	getPlayerId(): number
	getPlayerScore(playerId: number): number
	setTooltip(id: string, html: string): void
	setTooltipToClass(className: string, html: string): void
}

interface EnteringChooseActionArgs {
	canPass: boolean
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

interface NotifWinnerArgs {
	playerId: number
}

interface NotifScorePointArgs {
	playerId: number
	points: number
}

interface NotifMaterialMove {
	type: 'CARD' | 'TOKEN' | 'FIRST_PLAYER_TOKEN'
	from: 'HAND' | 'DECK' | 'STOCK'
	to: 'HAND' | 'DECK' | 'STOCK'
	fromArg: number
	toArg: number
	material: Array<any | string> //elements (cards for exemple), or tokenIds
}
