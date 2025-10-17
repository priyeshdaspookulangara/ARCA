<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\CCC\Domain\MessageRepositoryInterface;
use Modules\CRM\CCC\Domain\Model\Message;

class MessageController extends Controller
{
    private $messageRepository;

    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function index()
    {
        return $this->messageRepository->getAll();
    }

    public function store(Request $request)
    {
        $message = new Message($request->all());
        return $this->messageRepository->save($message);
    }

    public function show($id)
    {
        return $this->messageRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $message = $this->messageRepository->findById($id);
        $message->fill($request->all());
        return $this->messageRepository->save($message);
    }

    public function destroy($id)
    {
        $message = $this->messageRepository->findById($id);
        return response()->json(['success' => $this->messageRepository->delete($message)]);
    }
}