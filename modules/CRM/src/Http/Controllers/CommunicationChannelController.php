<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\CCC\Domain\CommunicationChannelRepositoryInterface;
use Modules\CRM\CCC\Domain\Model\CommunicationChannel;

class CommunicationChannelController extends Controller
{
    private $communicationChannelRepository;

    public function __construct(CommunicationChannelRepositoryInterface $communicationChannelRepository)
    {
        $this->communicationChannelRepository = $communicationChannelRepository;
    }

    public function index()
    {
        return $this->communicationChannelRepository->getAll();
    }

    public function store(Request $request)
    {
        $communicationChannel = new CommunicationChannel($request->all());
        return $this->communicationChannelRepository->save($communicationChannel);
    }

    public function show($id)
    {
        return $this->communicationChannelRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $communicationChannel = $this->communicationChannelRepository->findById($id);
        $communicationChannel->fill($request->all());
        return $this->communicationChannelRepository->save($communicationChannel);
    }

    public function destroy($id)
    {
        $communicationChannel = $this->communicationChannelRepository->findById($id);
        return response()->json(['success' => $this->communicationChannelRepository->delete($communicationChannel)]);
    }
}