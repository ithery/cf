<?php

/**
 * @see CPagination_Paginator
 */
class CPagination {
    /**
     * Indicate that Tailwind styling should be used for generated links.
     *
     * @return void
     */
    public static function useTailwind() {
        CPagination_Paginator::useTailwind();
    }

    /**
     * Indicate that Bootstrap 4 styling should be used for generated links.
     *
     * @return void
     */
    public static function useBootstrap() {
        CPagination_Paginator::useBootstrap();
    }

    /**
     * Indicate that Bootstrap 3 styling should be used for generated links.
     *
     * @return void
     */
    public static function useBootstrapThree() {
        CPagination_Paginator::useBootstrapThree();
    }

    /**
     * Indicate that Bootstrap 4 styling should be used for generated links.
     *
     * @return void
     */
    public static function useBootstrapFour() {
        CPagination_Paginator::useBootstrapFour();
    }

    /**
     * Indicate that Bootstrap 5 styling should be used for generated links.
     *
     * @return void
     */
    public static function useBootstrapFive() {
        CPagination_Paginator::useBootstrapFive();
    }
}
