<?php
/**
 * Created by PhpStorm.
 * User: Alexander Vitkalov
 * Date: 27.08.2019
 * Time: 16:46
 */

namespace app\Services;

use App\Book;
use DateInterval;
use DatePeriod;
use DateTime;
use DB;
use Exception;

class JournalStatService
{
	const DATE_PERIOD_FORMAT_SQL = '%Y-%m';
	const DATE_PERIOD_FORMAT = 'Y-m';

	/**
	 * Итоговые данные
	 *
	 * @var array
	 */
	private $data = [];

	/**
	 * Данные о книгах по месяцам
	 *
	 * @var array
	 */
	private $book_data = [];

	/**
	 * Книги
	 *
	 * @var array
	 */
	private $books = [];

    /**
     * JournalStatService constructor.
     */
    public function __construct()
    {
    }

	/**
	 * Возвращает данные о выданных книгах, сгруппированные по месяцам
	 *
	 * @return array
	 */
	private function select_by_month() {
		return DB::select( DB::raw( 'SELECT `book_id`, DATE_FORMAT(`created_at`, "' . self::DATE_PERIOD_FORMAT_SQL . '") AS period, COUNT(`book_id`) AS total
				FROM `journal`
				GROUP BY DATE_FORMAT(`created_at`, "' . self::DATE_PERIOD_FORMAT_SQL . '"),`book_id`' ) );
	}

	/**
	 * Получит все книги в виде массива [ book_id => title ]
	 */
	private function get_books() {
		$results = Book::get()->toArray();
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$this->books[ $result['id'] ] = $result['title'];
			}
		}
	}

	/**
	 * Получит данные по месяцам в виде массива [ period => data ]
	 */
	private function get_data_by_month() {
		$results = $this->select_by_month();

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$this->book_data[ $result->period ][ $result->book_id ] = $result->total;
			}
		}
	}

	/**
	 * Заполнит данные месяца недостающими книгами и вернёт общее количество книг
	 *
	 * @param $period string
	 *
	 * @return int
	 */
	private function handle_month( $period ) {
		$month_sum = 0;

		foreach ( $this->books as $book_id => $book_title ) {
			$item = [
				'date'  => $period,
				'title' => $book_title,
				'value' => 0,
			];

			if ( isset( $this->book_data[ $period ][ $book_id ] ) ) {
				$item['value'] = $this->book_data[ $period ][ $book_id ];
				$month_sum     += $item['value'];
			}

			$this->data[] = $item;
		}

		$this->data[] = [
			'date'  => $period,
			'value' => $month_sum,
		];

		return $month_sum;
	}

	/**
	 * Пройдётся по всем месяцам и заполнит массив недостающими данными
	 */
	private function handle_data() {
		$year     = '2017'; // Год начала статистики
		$year_sum = 0;      // Сумма за год
		$sum      = 0;      // Общая сумма
		// Начнём с самой ранней даты, указанной в базе данных
		$begin = new DateTime( '2017-08-01' );
		$end   = new DateTime( '2019-08-31' );

		$interval = DateInterval::createFromDateString( '1 month' );
		$period   = new DatePeriod( $begin, $interval, $end );

		foreach ( $period as $dt ) {
			// Если начался новый год
			if ( $year != $dt->format( "Y" ) ) {
				$this->data[] = [
					'date'  => $year,
					'value' => $year_sum,
				];
				$sum          += $year_sum;
				$year_sum     = 0;
				$year         = $dt->format( "Y" );
			}

			$s_period = $dt->format( self::DATE_PERIOD_FORMAT );
			$year_sum += $this->handle_month( $s_period );
		}

		$sum          += $year_sum;
		$this->data[] = [
			'value' => $sum,
		];
	}

	/**
	 * Вернёт итоговые данные
	 *
	 * @return array
	 */
    public function get_data()
    {
    	$this->get_books();
    	$this->get_data_by_month();
    	$this->handle_data();

        return $this->data;
    }
}
