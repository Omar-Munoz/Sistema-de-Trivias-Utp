<?php
return [
  // PUBLIC
  ['GET',  '/',                       'PublicController@home'],
  ['GET',  '/register',               'AuthController@registerForm'],
  ['POST', '/register',               'AuthController@register'],
  ['GET',  '/login',                  'AuthController@loginForm'],
  ['POST', '/login',                  'AuthController@login'],
  ['GET',  '/logout',                 'AuthController@logout'],
  ['GET',  '/progress',               'PublicController@progress'],
  ['GET',  '/my-prizes',              'PublicController@myPrizes'],

  // PLAYER AVATAR
  ['POST', '/player/avatar/upload',   'PlayerAvatarController@upload'],

  // GAME
  ['GET',  '/play',                   'GameController@selectTopic'],
  ['POST', '/play/start',             'GameController@start'],
  ['GET',  '/play/session',           'GameController@session'],
  ['POST', '/play/answer',            'GameController@answer'],
  ['GET',  '/play/finish',            'GameController@finish'],
['GET',  '/play/result',  'GameController@result'],
  // QR
  ['GET',  '/qr/set',                 'GameController@qrJoin'],
  ['GET',  '/qr/leaderboard',         'GameController@leaderboard'],

  // ADMIN
  ['GET',  '/admin',                  'AdminDashboardController@index'],

  // USERS (CRUD + avatar toggle)
  ['GET',  '/admin/users',            'UsersController@index'],
  ['POST', '/admin/users/create',     'UsersController@create'],
  ['POST', '/admin/users/update',     'UsersController@update'],
  ['POST', '/admin/users/delete',     'UsersController@delete'],
  ['POST', '/admin/users/avatar/toggle','UsersController@toggleAvatar'],
  ['GET',  '/admin/avatars',          'AvatarsController@index'],
['POST', '/admin/avatars/create',   'AvatarsController@create'],
['POST', '/admin/avatars/toggle',   'AvatarsController@toggle'],
['POST', '/admin/avatars/delete',   'AvatarsController@delete'],
['POST', '/player/avatar/clear', 'PlayerAvatarController@clear'],



  // QUESTIONS (CRUD + edit)
  ['GET',  '/admin/questions',        'QuestionsController@index'],
  ['POST', '/admin/questions/create', 'QuestionsController@create'],
  ['POST', '/admin/questions/update', 'QuestionsController@update'],
  ['POST', '/admin/questions/delete', 'QuestionsController@delete'],

  // SETS (CRUD + QR auto)
  ['GET',  '/admin/sets',             'SetsController@index'],
  ['POST', '/admin/sets/create',      'SetsController@create'],
  ['POST', '/admin/sets/update',      'SetsController@update'],
  ['POST', '/admin/sets/delete',      'SetsController@delete'],

  // PRIZES (CRUD + edit)
  ['GET',  '/admin/prizes',           'PrizesController@index'],
  ['POST', '/admin/prizes/create',    'PrizesController@create'],
  ['POST', '/admin/prizes/update',    'PrizesController@update'],
  ['POST', '/admin/prizes/delete',    'PrizesController@delete'],

  // REPORTS
  ['GET',  '/admin/reports/excel',    'ReportsController@excel'],
  // ADMIN - TOPICS
['GET',  '/admin/topics',        'TopicsController@index'],
['POST', '/admin/topics/create', 'TopicsController@create'],
['POST', '/admin/topics/update', 'TopicsController@update'],
['POST', '/admin/topics/delete', 'TopicsController@delete'],

];
